<?php

namespace App\Services;

use App\Models\Batch;
use App\Models\Delivery;
use App\Models\Item;
use App\Models\Request;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportService
{
    /**
     * Get stock on hand data for all active catalog items.
     *
     * @return array<int, array{
     *     code: string,
     *     description: string,
     *     category: string,
     *     unit_of_measure: string,
     *     consignor: string,
     *     minimum_level: int,
     *     current_stock: int,
     *     is_low_stock: bool,
     *     active_batches_count: int,
     *     unit_cost: float,
     *     total_valuation: float,
     * }>
     */
    public function getStockOnHandData(): array
    {
        $items = Item::with(['consignor', 'batches' => fn ($q) => $q->where('quantity_remaining', '>', 0)->where('is_active', true)])
            ->where('is_active', true)
            ->orderBy('description', 'asc')
            ->get();

        return $items->map(function (Item $item) {
            $currentStock = (int) $item->batches->sum('quantity_remaining');
            $totalValuation = (float) $item->batches->sum(fn (Batch $b) => $b->quantity_remaining * (float) $b->unit_cost);
            $avgUnitCost = $currentStock > 0 ? ($totalValuation / $currentStock) : 0.0;

            return [
                'code' => $item->code,
                'description' => $item->description,
                'category' => $item->category->getLabel(),
                'unit_of_measure' => $item->unit_of_measure,
                'consignor' => $item->consignor?->name ?? 'Unassigned',
                'minimum_level' => $item->minimum_level,
                'current_stock' => $currentStock,
                'is_low_stock' => $currentStock <= $item->minimum_level,
                'active_batches_count' => $item->batches->count(),
                'unit_cost' => $avgUnitCost,
                'total_valuation' => $totalValuation,
            ];
        })->all();
    }

    /**
     * Get batches expiring within 90 days (or already expired) with depot stock remaining.
     */
    public function getNearExpiryData(int $days = 90): array
    {
        $today = Carbon::today('Asia/Manila');
        $cutoff = $today->copy()->addDays($days);

        $batches = Batch::with(['item.consignor', 'delivery.consignor'])
            ->where('quantity_remaining', '>', 0)
            ->where('is_active', true)
            ->where('expiration_date', '<=', $cutoff)
            ->orderBy('expiration_date', 'asc')
            ->get();

        return $batches->map(function (Batch $batch) use ($today) {
            $daysLeft = (int) $today->diffInDays($batch->expiration_date, false);

            return [
                'lot_number' => $batch->lot_number,
                'item_code' => $batch->item?->code ?? '-',
                'item_description' => $batch->item?->description ?? '-',
                'category' => $batch->item?->category?->getLabel() ?? '-',
                'consignor' => $batch->delivery?->consignor?->name ?? $batch->item?->consignor?->name ?? '-',
                'dr_number' => $batch->delivery?->dr_number ?? '-',
                'expiration_date' => $batch->expiration_date->format('M j, Y'),
                'days_left' => $daysLeft,
                'status' => $daysLeft < 0 ? 'EXPIRED' : "Expiring in {$daysLeft} days",
                'is_expired' => $daysLeft < 0,
                'quantity_remaining' => $batch->quantity_remaining,
                'unit_cost' => (float) $batch->unit_cost,
                'value_at_risk' => (float) ($batch->quantity_remaining * (float) $batch->unit_cost),
            ];
        })->all();
    }

    /**
     * Get delivery history data filtered by date and consignor.
     */
    public function getDeliveryHistoryData(?string $fromDate = null, ?string $toDate = null, ?int $consignorId = null): array
    {
        $query = Delivery::with(['consignor', 'receivedBy', 'batches.item'])
            ->orderBy('delivery_date', 'desc');

        if ($fromDate) {
            $query->whereDate('delivery_date', '>=', $fromDate);
        }
        if ($toDate) {
            $query->whereDate('delivery_date', '<=', $toDate);
        }
        if ($consignorId) {
            $query->where('consignor_id', $consignorId);
        }

        return $query->get()->map(function (Delivery $delivery) {
            return [
                'dr_number' => $delivery->dr_number,
                'delivery_date' => $delivery->delivery_date->format('M j, Y'),
                'consignor' => $delivery->consignor?->name ?? '-',
                'received_by' => $delivery->receivedBy?->name ?? '-',
                'batches_count' => $delivery->batches->count(),
                'total_units' => $delivery->total_units,
                'total_amount' => $delivery->total_amount,
                'notes' => $delivery->notes ?? '',
            ];
        })->all();
    }

    /**
     * Get unit requisition history filtered by date, unit, status, and urgency.
     */
    public function getUnitRequestsData(?string $fromDate = null, ?string $toDate = null, ?int $unitId = null, ?string $status = null, ?bool $isUrgent = null): array
    {
        $query = Request::with(['unit', 'requestedBy', 'fulfilledBy', 'lines.item'])
            ->orderBy('request_date', 'desc');

        if ($fromDate) {
            $query->whereDate('request_date', '>=', $fromDate);
        }
        if ($toDate) {
            $query->whereDate('request_date', '<=', $toDate);
        }
        if ($unitId) {
            $query->where('unit_id', $unitId);
        }
        if ($status) {
            $query->where('status', $status);
        }
        if ($isUrgent !== null) {
            $query->where('is_urgent', $isUrgent);
        }

        return $query->get()->map(function (Request $req) {
            return [
                'request_number' => $req->request_number,
                'request_date' => $req->request_date->format('M j, Y • g:i A'),
                'unit' => $req->unit?->name ?? '-',
                'requested_by' => $req->requestedBy?->name ?? '-',
                'is_urgent' => $req->is_urgent ? 'STAT (Urgent)' : 'Regular',
                'urgent_reason' => $req->urgent_reason ?? '-',
                'total_requested' => $req->total_requested,
                'total_issued' => $req->total_issued,
                'status' => $req->status->getLabel(),
                'fulfilled_by' => $req->fulfilledBy?->name ?? '-',
                'fulfilled_at' => $req->fulfilled_at?->format('M j, Y • g:i A') ?? '-',
            ];
        })->all();
    }

    /**
     * Render a view into a downloadable PDF using Dompdf.
     */
    public function renderPdf(string $view, array $data, string $filename, string $orientation = 'portrait'): StreamedResponse
    {
        $options = new Options;
        $options->set('defaultFont', 'Helvetica');
        $options->set('isRemoteEnabled', false);
        $options->set('isHtml5ParserEnabled', true);

        $dompdf = new Dompdf($options);
        $html = view($view, array_merge($data, [
            'generatedAt' => Carbon::now('Asia/Manila')->format('F j, Y • g:i A'),
            'generatedBy' => auth()->user()?->name ?? 'RMPH Staff',
        ]))->render();

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', $orientation);
        $dompdf->render();

        return response()->streamDownload(
            fn () => print ($dompdf->output()),
            $filename,
            ['Content-Type' => 'application/pdf']
        );
    }

    /**
     * Stream a downloadable UTF-8 CSV with Excel BOM.
     *
     * @param  array<int, string>  $headers
     * @param  array<int, array<int, mixed>>  $rows
     */
    public function renderCsv(array $headers, array $rows, string $filename): StreamedResponse
    {
        $responseHeaders = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->streamDownload(function () use ($headers, $rows) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM so Microsoft Excel opens it seamlessly without character corruption
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, $headers);

            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }

            fclose($handle);
        }, $filename, $responseHeaders);
    }
}
