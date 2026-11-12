<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function __construct(
        protected ReportService $reportService
    ) {}

    public function stockOnHandPdf(): StreamedResponse
    {
        $this->authorizeAccess();
        $items = $this->reportService->getStockOnHandData();
        $date = Carbon::now('Asia/Manila')->format('Ymd_His');

        return $this->reportService->renderPdf(
            'reports.stock-on-hand',
            ['items' => $items],
            "RMPH_Stock_On_Hand_{$date}.pdf",
            'portrait'
        );
    }

    public function stockOnHandCsv(): StreamedResponse
    {
        $this->authorizeAccess();
        $items = $this->reportService->getStockOnHandData();
        $headers = ['Item Code', 'Description', 'Category', 'Unit of Measure', 'Consignor', 'Min. Alert', 'Stock on Hand', 'Status'];

        $rows = array_map(function ($item) {
            return [
                $item['code'],
                $item['description'],
                $item['category'],
                $item['unit_of_measure'],
                $item['consignor'],
                $item['minimum_level'],
                $item['current_stock'],
                $item['is_low_stock'] ? 'Low Stock' : 'Healthy',
            ];
        }, $items);

        $date = Carbon::now('Asia/Manila')->format('Ymd_His');

        return $this->reportService->renderCsv($headers, $rows, "RMPH_Stock_On_Hand_{$date}.csv");
    }

    public function nearExpiryPdf(): StreamedResponse
    {
        $this->authorizeAccess();
        $batches = $this->reportService->getNearExpiryData(90);
        $date = Carbon::now('Asia/Manila')->format('Ymd_His');

        return $this->reportService->renderPdf(
            'reports.near-expiry',
            ['batches' => $batches],
            "RMPH_Near_Expiry_90Days_{$date}.pdf",
            'portrait'
        );
    }

    public function nearExpiryCsv(): StreamedResponse
    {
        $this->authorizeAccess();
        $batches = $this->reportService->getNearExpiryData(90);
        $headers = ['Lot Number', 'Item Code', 'Description', 'Category', 'Consignor', 'DR Number', 'Expiration Date', 'Days Left', 'Status', 'Units at Risk'];

        $rows = array_map(function ($b) {
            return [
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
        }, $batches);

        $date = Carbon::now('Asia/Manila')->format('Ymd_His');

        return $this->reportService->renderCsv($headers, $rows, "RMPH_Near_Expiry_90Days_{$date}.csv");
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
            ['deliveries' => $deliveries],
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
        $headers = ['DR Number', 'Delivery Date', 'Consignor', 'Received By', 'Batches Count', 'Total Units'];

        $rows = array_map(function ($d) {
            return [
                $d['dr_number'],
                $d['delivery_date'],
                $d['consignor'],
                $d['received_by'],
                $d['batches_count'],
                $d['total_units'],
            ];
        }, $deliveries);

        $date = Carbon::now('Asia/Manila')->format('Ymd_His');

        return $this->reportService->renderCsv($headers, $rows, "RMPH_Delivery_History_{$date}.csv");
    }

    public function unitRequestsPdf(Request $request): StreamedResponse
    {
        $this->authorizeAccess();
        $requests = $this->reportService->getUnitRequestsData(
            $request->query('from_date'),
            $request->query('to_date'),
            $request->query('unit_id') ? (int) $request->query('unit_id') : null,
            $request->query('status'),
            $request->has('is_urgent') ? $request->boolean('is_urgent') : null
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
        $requests = $this->reportService->getUnitRequestsData(
            $request->query('from_date'),
            $request->query('to_date'),
            $request->query('unit_id') ? (int) $request->query('unit_id') : null,
            $request->query('status'),
            $request->has('is_urgent') ? $request->boolean('is_urgent') : null
        );
        $headers = ['Request Number', 'Unit', 'Request Date', 'Priority', 'Units Requested', 'Units Issued', 'Status', 'Requested By'];

        $rows = array_map(function ($r) {
            return [
                $r['request_number'],
                $r['unit'],
                $r['request_date'],
                $r['is_urgent'],
                $r['total_requested'],
                $r['total_issued'],
                $r['status'],
                $r['requested_by'],
            ];
        }, $requests);

        $date = Carbon::now('Asia/Manila')->format('Ymd_His');

        return $this->reportService->renderCsv($headers, $rows, "RMPH_Unit_Requests_History_{$date}.csv");
    }

    protected function authorizeAccess(): void
    {
        abort_unless(auth()->user()?->hasAnyRole(['admin', 'custodian', 'custodian_supplies', 'custodian_drugs', 'viewer']), 403, 'Unauthorized access to consignment inventory reports.');
    }
}
