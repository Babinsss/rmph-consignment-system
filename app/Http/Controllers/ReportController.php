<?php

namespace App\Http\Controllers;

use App\Models\Request as Requisition;
use App\Services\ReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function __construct(
        protected ReportService $reportService
    ) {}

    public function stockOnHandPdf(Request $request): StreamedResponse
    {
        $this->authorizeAccess();
        $items = $this->reportService->getStockOnHandData(
            $request->query('category'),
            $request->boolean('low_stock_only')
        );
        $date = Carbon::now('Asia/Manila')->format('Ymd_His');

        return $this->reportService->renderPdf(
            'reports.stock-on-hand',
            [
                'items' => $items,
                'showCosts' => auth()->user()?->canViewUnitCosts() ?? false,
                'lowStockOnly' => $request->boolean('low_stock_only'),
            ],
            "RMPH_Stock_On_Hand_{$date}.pdf",
            'landscape'
        );
    }

    public function stockOnHandCsv(Request $request): StreamedResponse
    {
        $this->authorizeAccess();
        $items = $this->reportService->getStockOnHandData(
            $request->query('category'),
            $request->boolean('low_stock_only')
        );
        $showCosts = auth()->user()?->canViewUnitCosts() ?? false;

        $headers = ['Item Code', 'Description', 'Category', 'Unit of Measure', 'Consignor', 'Min. Alert', 'Stock on Hand', 'Status'];
        if ($showCosts) {
            $headers[] = 'Avg. Unit Cost (PHP)';
            $headers[] = 'Total Valuation (PHP)';
        }

        $rows = array_map(function ($item) use ($showCosts) {
            $row = [
                $item['code'],
                $item['description'],
                $item['category'],
                $item['unit_of_measure'],
                $item['consignor'],
                $item['minimum_level'],
                $item['current_stock'],
                $item['is_low_stock'] ? 'Low Stock' : 'Healthy',
            ];

            if ($showCosts) {
                $row[] = number_format($item['unit_cost'], 2, '.', '');
                $row[] = number_format($item['total_valuation'], 2, '.', '');
            }

            return $row;
        }, $items);

        $date = Carbon::now('Asia/Manila')->format('Ymd_His');

        return $this->reportService->renderCsv($headers, $rows, "RMPH_Stock_On_Hand_{$date}.csv");
    }

    public function nearExpiryPdf(Request $request): StreamedResponse
    {
        $this->authorizeAccess();
        $days = max(1, min(365, (int) $request->query('days', 90)));
        $batches = $this->reportService->getNearExpiryData($days);
        $date = Carbon::now('Asia/Manila')->format('Ymd_His');

        return $this->reportService->renderPdf(
            'reports.near-expiry',
            [
                'batches' => $batches,
                'days' => $days,
                'showCosts' => auth()->user()?->canViewUnitCosts() ?? false,
            ],
            "RMPH_Near_Expiry_{$days}Days_{$date}.pdf",
            'landscape'
        );
    }

    public function nearExpiryCsv(Request $request): StreamedResponse
    {
        $this->authorizeAccess();
        $days = max(1, min(365, (int) $request->query('days', 90)));
        $batches = $this->reportService->getNearExpiryData($days);
        $showCosts = auth()->user()?->canViewUnitCosts() ?? false;

        $headers = ['Lot Number', 'Item Code', 'Description', 'Category', 'Consignor', 'DR Number', 'Expiration Date', 'Days Left', 'Status', 'Units at Risk'];
        if ($showCosts) {
            $headers[] = 'Unit Cost (PHP)';
            $headers[] = 'Value at Risk (PHP)';
        }

        $rows = array_map(function ($b) use ($showCosts) {
            $row = [
                $b['lot_number'],
                $b['item_code'],
                $b['item_description'],
                $b['category'],
                $b['consignor'],
                $b['dr_number'],
                $b['expiration_date'],
                $b['days_left'],
                $b['status'],
                $b['quantity_remaining'],
            ];

            if ($showCosts) {
                $row[] = number_format($b['unit_cost'], 2, '.', '');
                $row[] = number_format($b['value_at_risk'], 2, '.', '');
            }

            return $row;
        }, $batches);

        $date = Carbon::now('Asia/Manila')->format('Ymd_His');

        return $this->reportService->renderCsv($headers, $rows, "RMPH_Near_Expiry_{$days}Days_{$date}.csv");
    }

    public function deliveriesPdf(Request $request): StreamedResponse
    {
        $this->authorizeAccess();
        $deliveries = $this->reportService->getDeliveryHistoryData(
            $request->query('from_date'),
            $request->query('to_date'),
            $request->query('consignor_id') ? (int) $request->query('consignor_id') : null
        );
        $date = Carbon::now('Asia/Manila')->format('Ymd_His');

        return $this->reportService->renderPdf(
            'reports.deliveries',
            [
                'deliveries' => $deliveries,
                'showCosts' => auth()->user()?->canViewUnitCosts() ?? false,
            ],
            "RMPH_Delivery_History_{$date}.pdf",
            'portrait'
        );
    }

    public function deliveriesCsv(Request $request): StreamedResponse
    {
        $this->authorizeAccess();
        $deliveries = $this->reportService->getDeliveryHistoryData(
            $request->query('from_date'),
            $request->query('to_date'),
            $request->query('consignor_id') ? (int) $request->query('consignor_id') : null
        );
        $showCosts = auth()->user()?->canViewUnitCosts() ?? false;

        $headers = ['DR Number', 'Delivery Date', 'Consignor', 'Received By', 'Batches Count', 'Total Units'];
        if ($showCosts) {
            $headers[] = 'Total Amount (PHP)';
        }

        $rows = array_map(function ($d) use ($showCosts) {
            $row = [
                $d['dr_number'],
                $d['delivery_date'],
                $d['consignor'],
                $d['received_by'],
                $d['batches_count'],
                $d['total_units'],
            ];

            if ($showCosts) {
                $row[] = number_format($d['total_amount'], 2, '.', '');
            }

            return $row;
        }, $deliveries);

        $date = Carbon::now('Asia/Manila')->format('Ymd_His');

        return $this->reportService->renderCsv($headers, $rows, "RMPH_Delivery_History_{$date}.csv");
    }

    public function unitRequestsPdf(Request $request): StreamedResponse
    {
        $this->authorizeAccess();
        $isUrgent = $request->filled('is_urgent') ? $request->boolean('is_urgent') : null;
        $requests = $this->reportService->getUnitRequestsData(
            $request->query('from_date'),
            $request->query('to_date'),
            $request->query('unit_id') ? (int) $request->query('unit_id') : null,
            $request->query('status') ?: null,
            $isUrgent
        );
        $date = Carbon::now('Asia/Manila')->format('Ymd_His');

        return $this->reportService->renderPdf(
            'reports.unit-requests',
            ['requests' => $requests],
            "RMPH_Unit_Requests_History_{$date}.pdf",
            'portrait'
        );
    }

    public function unitRequestsCsv(Request $request): StreamedResponse
    {
        $this->authorizeAccess();
        $isUrgent = $request->filled('is_urgent') ? $request->boolean('is_urgent') : null;
        $requests = $this->reportService->getUnitRequestsData(
            $request->query('from_date'),
            $request->query('to_date'),
            $request->query('unit_id') ? (int) $request->query('unit_id') : null,
            $request->query('status') ?: null,
            $isUrgent
        );
        $headers = ['Request Number', 'Unit', 'Request Date', 'Priority', 'Entry Mode', 'Units Requested', 'Units Issued', 'Status', 'Requested By', 'Fulfilled By'];

        $rows = array_map(function ($r) {
            return [
                $r['request_number'],
                $r['unit'],
                $r['request_date'],
                $r['is_urgent'],
                $r['is_downtime'],
                $r['total_requested'],
                $r['total_issued'],
                $r['status'],
                $r['requested_by'],
                $r['fulfilled_by'],
            ];
        }, $requests);

        $date = Carbon::now('Asia/Manila')->format('Ymd_His');

        return $this->reportService->renderCsv($headers, $rows, "RMPH_Unit_Requests_History_{$date}.csv");
    }

    public function stockMovementsPdf(Request $request): StreamedResponse
    {
        $this->authorizeAccess();
        $movements = $this->reportService->getStockMovementsData(
            $request->query('from_date'),
            $request->query('to_date'),
            $request->query('type') ?: null,
            $request->query('item_id') ? (int) $request->query('item_id') : null
        );
        $date = Carbon::now('Asia/Manila')->format('Ymd_His');

        return $this->reportService->renderPdf(
            'reports.stock-movements',
            ['movements' => $movements],
            "RMPH_Stock_Card_History_{$date}.pdf",
            'landscape'
        );
    }

    public function stockMovementsCsv(Request $request): StreamedResponse
    {
        $this->authorizeAccess();
        $movements = $this->reportService->getStockMovementsData(
            $request->query('from_date'),
            $request->query('to_date'),
            $request->query('type') ?: null,
            $request->query('item_id') ? (int) $request->query('item_id') : null
        );
        $headers = ['Timestamp', 'Movement Date', 'Type', 'Reference #', 'Item Code', 'Description', 'Lot / Batch #', 'Qty Changed', 'Balance After', 'Transacted By', 'Reason / Destination'];

        $rows = array_map(function ($m) {
            return [
                $m['timestamp'],
                $m['movement_date'],
                $m['type'],
                $m['reference_number'],
                $m['item_code'],
                $m['item_description'],
                $m['lot_number'],
                $m['quantity'],
                $m['balance_after'],
                $m['user'],
                $m['reason'],
            ];
        }, $movements);

        $date = Carbon::now('Asia/Manila')->format('Ymd_His');

        return $this->reportService->renderCsv($headers, $rows, "RMPH_Stock_Card_History_{$date}.csv");
    }

    /**
     * Download an operational Requisition & Issue Slip PDF (no unit costs).
     * Accessible to authorized operations staff and to requesting unit staff for their own unit.
     */
    public function requestIssueSlipPdf(Requisition $requisition): StreamedResponse
    {
        $user = auth()->user();
        abort_unless($user !== null && $user->is_active, 403);

        $isOpsUser = $user->hasAnyRole(['admin', 'custodian', 'custodian_supplies', 'custodian_drugs', 'viewer']);
        $isOwningWard = $user->hasRole('requesting_unit') && $user->unit_id === $requisition->unit_id;

        abort_unless($isOpsUser || $isOwningWard, 403, 'Unauthorized access to requisition issue slip.');

        $requisition->load(['unit', 'requestedBy', 'fulfilledBy', 'lines.item']);
        $issuedMovements = $requisition->getIssuedMovements();

        return $this->reportService->renderPdf(
            'reports.issue-slip',
            [
                'requisition' => $requisition,
                'issuedMovements' => $issuedMovements,
            ],
            "RMPH_Issue_Slip_{$requisition->request_number}.pdf",
            'portrait'
        );
    }

    protected function authorizeAccess(): void
    {
        abort_unless(auth()->user()?->hasAnyRole(['admin', 'custodian', 'custodian_supplies', 'custodian_drugs', 'viewer']), 403, 'Unauthorized access to consignment inventory reports.');
    }
}
