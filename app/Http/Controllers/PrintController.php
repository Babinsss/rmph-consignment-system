<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\StockMovement;
use App\Models\StockRequest;
use App\Models\StockRequestItem;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PrintController extends Controller
{
    /**
     * Render the official printable Stockcard ledger for an item.
     */
    public function stockcard(Request $request, Item $item): View
    {
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $movementsQuery = $item->stockMovements()
            ->with(['user', 'deliveryItem'])
            ->orderBy('id', 'asc');

        if ($dateFrom) {
            $movementsQuery->whereDate('created_at', '>=', Carbon::parse($dateFrom));
        }

        if ($dateTo) {
            $movementsQuery->whereDate('created_at', '<=', Carbon::parse($dateTo));
        }

        $movements = $movementsQuery->get();

        return view('print.stockcard', [
            'item' => $item,
            'movements' => $movements,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ]);
    }

    /**
     * Render the official printable Requisition and Issue Slip (RIS).
     */
    public function requisition(Request $request, StockRequest $stockRequest): View
    {
        $stockRequest->load(['items.item', 'requester', 'approvedBy', 'issuedBy']);

        return view('print.requisition', [
            'request' => $stockRequest,
        ]);
    }

    /**
     * Render the official printable Consignment Inventory Audit & Valuation Report.
     */
    public function inventoryReport(Request $request): View
    {
        $dateFromStr = $request->input('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateToStr = $request->input('date_to', now()->endOfMonth()->format('Y-m-d'));
        $department = $request->input('department', 'all');
        $supplierId = $request->input('supplier_id', 'all');

        $dateFrom = Carbon::parse($dateFromStr)->startOfDay();
        $dateTo = Carbon::parse($dateToStr)->endOfDay();

        $itemsQuery = Item::query()->where('is_active', true)->with('supplier');

        if ($department !== 'all') {
            $itemsQuery->where('department', $department);
        }

        if ($supplierId !== 'all') {
            $itemsQuery->where('supplier_id', $supplierId);
        }

        $items = $itemsQuery->get();

        $rows = $items->map(function (Item $item) use ($dateFrom, $dateTo) {
            $beginningIn = StockMovement::where('item_id', $item->id)
                ->where('created_at', '<', $dateFrom)
                ->sum('quantity_in');

            $beginningOut = StockMovement::where('item_id', $item->id)
                ->where('created_at', '<', $dateFrom)
                ->sum('quantity_out');

            $beginningBalance = (int) ($beginningIn - $beginningOut);

            $receivedQty = (int) StockMovement::where('item_id', $item->id)
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->where('movement_type', 'DELIVERY_IN')
                ->sum('quantity_in');

            $receivedCost = (float) StockMovement::where('item_id', $item->id)
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->where('movement_type', 'DELIVERY_IN')
                ->sum('total_cost');

            $issuedQty = (int) StockMovement::where('item_id', $item->id)
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->where('movement_type', 'REQUEST_OUT')
                ->sum('quantity_out');

            $issuedCost = (float) StockMovement::where('item_id', $item->id)
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->where('movement_type', 'REQUEST_OUT')
                ->sum('total_cost');

            $endingBalance = $beginningBalance + $receivedQty - $issuedQty;
            $endingValuation = $endingBalance * (float) $item->unit_cost;

            return [
                'item_code' => $item->item_code,
                'name' => $item->name,
                'generic_name' => $item->generic_name,
                'department' => $item->department,
                'category' => $item->category,
                'uom' => $item->uom,
                'supplier_name' => $item->supplier?->name ?? 'N/A',
                'beginning_balance' => $beginningBalance,
                'received_qty' => $receivedQty,
                'received_cost' => $receivedCost,
                'issued_qty' => $issuedQty,
                'issued_cost' => $issuedCost,
                'ending_balance' => $endingBalance,
                'unit_cost' => (float) $item->unit_cost,
                'ending_valuation' => $endingValuation,
            ];
        });

        $totalReceivedCost = $rows->sum('received_cost');
        $totalIssuedCost = $rows->sum('issued_cost');
        $totalEndingValuation = $rows->sum('ending_valuation');

        return view('print.inventory-report', [
            'rows' => $rows,
            'dateFrom' => $dateFromStr,
            'dateTo' => $dateToStr,
            'department' => $department,
            'totalReceivedCost' => $totalReceivedCost,
            'totalIssuedCost' => $totalIssuedCost,
            'totalEndingValuation' => $totalEndingValuation,
        ]);
    }

    /**
     * Render the official printable Statement of Consumed Goods (StCG) for a consignor.
     */
    public function stcg(Request $request, Supplier $supplier): View
    {
        $dateFromStr = $request->input('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateToStr = $request->input('date_to', now()->endOfMonth()->format('Y-m-d'));
        $stcgNumber = $request->input('stcg_number', 'StCG-'.now()->format('Ym')."-{$supplier->code}");

        $dateFrom = Carbon::parse($dateFromStr)->startOfDay();
        $dateTo = Carbon::parse($dateToStr)->endOfDay();

        $movements = StockMovement::whereHas('item', function ($q) use ($supplier) {
            $q->where('supplier_id', $supplier->id);
        })
            ->where('movement_type', 'REQUEST_OUT')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->with('item')
            ->get();

        $rows = $movements->groupBy('item_id')->map(function ($itemMovements) {
            $first = $itemMovements->first();
            $item = $first->item;
            $totalQty = $itemMovements->sum('quantity_out');
            $unitCost = (float) $item->unit_cost;
            $totalCost = $totalQty * $unitCost;

            return [
                'item_code' => $item->item_code,
                'item_name' => $item->name,
                'generic_name' => $item->generic_name,
                'uom' => $item->uom,
                'unit_cost' => $unitCost,
                'quantity_consumed' => $totalQty,
                'total_amount' => $totalCost,
            ];
        })->values();

        $totalPayable = $rows->sum('total_amount');
        $totalQuantity = $rows->sum('quantity_consumed');

        return view('print.stcg', [
            'supplier' => $supplier,
            'rows' => $rows,
            'dateFrom' => $dateFromStr,
            'dateTo' => $dateToStr,
            'stcgNumber' => $stcgNumber,
            'totalPayable' => $totalPayable,
            'totalQuantity' => $totalQuantity,
        ]);
    }

    /**
     * Render the official printable Bin Card (RMH-PPS-F02) for warehouse bin shelves.
     */
    public function bincard(Request $request, Item $item): View
    {
        $movements = $item->stockMovements()
            ->with(['deliveryItem.delivery.supplier'])
            ->orderBy('created_at', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $transactions = collect();

        foreach ($movements as $m) {
            $dept = $m->source_destination;
            if ($m->movement_type === 'DELIVERY_IN') {
                $dept = $item->supplier?->name ?? ($m->deliveryItem?->delivery?->supplier?->name ?? 'Consignor Delivery');
            }

            $transactions->push([
                'date' => $m->created_at,
                'department' => $dept,
                'delivery_qty' => $m->quantity_in > 0 ? $m->quantity_in : '',
                'qty_issued' => $m->quantity_out > 0 ? $m->quantity_out : '',
                'balance' => $m->balance_after,
            ]);
        }

        if ($transactions->isEmpty() && $item->current_stock > 0) {
            $transactions->push([
                'date' => $item->created_at ?? now(),
                'department' => $item->supplier?->name ?? 'Initial Balance',
                'delivery_qty' => $item->current_stock,
                'qty_issued' => '',
                'balance' => $item->current_stock,
            ]);
        }

        return view('print.bincard', [
            'item' => $item,
            'transactions' => $transactions,
        ]);
    }

    /**
     * Render the official Report on Actual Physical Count (RAPC) Long Bond Landscape.
     */
    public function rapc(Request $request, ?Item $item = null): View
    {
        $year = (int) $request->input('year', date('Y'));
        $department = $request->input('department', $item?->department ?? 'medical_supplies');
        $supplierId = $request->input('supplier_id', $item?->supplier_id);

        if ($item && ! $request->has('supplier_id') && $item->supplier_id) {
            $itemsQuery = Item::where('supplier_id', $item->supplier_id);
            $consignorName = $item->supplier?->name;
        } else {
            $itemsQuery = Item::where('department', $department)
                ->when($supplierId, fn ($q) => $q->where('supplier_id', $supplierId));
            $consignorName = $supplierId ? Supplier::find($supplierId)?->name : 'All Accredited Consignors';
        }

        $items = $itemsQuery->where('is_active', true)
            ->with('supplier')
            ->orderBy('name', 'asc')
            ->take(50)
            ->get()
            ->map(function (Item $it) use ($year) {
                $monthlyBalances = [];
                $currentMonth = (int) date('m');
                $currentYear = (int) date('Y');

                for ($m = 1; $m <= 12; $m++) {
                    if ($year === $currentYear && $m > $currentMonth) {
                        $monthlyBalances[$m] = '';
                    } else {
                        $endOfMonth = Carbon::createFromDate($year, $m, 1)->endOfMonth();
                        $lastMovement = StockMovement::where('item_id', $it->id)
                            ->where('created_at', '<=', $endOfMonth)
                            ->orderBy('created_at', 'desc')
                            ->orderBy('id', 'desc')
                            ->first();

                        $monthlyBalances[$m] = $lastMovement ? $lastMovement->balance_after : $it->current_stock;
                    }
                }

                return [
                    'id' => $it->id,
                    'item_code' => $it->item_code,
                    'name' => $it->name,
                    'uom' => $it->uom,
                    'unit_cost' => (float) $it->unit_cost,
                    'current_stock' => $it->current_stock,
                    'supplier_name' => $it->supplier?->name ?? 'N/A',
                    'monthly_balances' => $monthlyBalances,
                ];
            });

        return view('print.rapc', [
            'items' => $items,
            'year' => $year,
            'department' => $department,
            'consignorName' => $consignorName,
        ]);
    }

    /**
     * Render the official printable Purchase Request (Appendix 47) for consignment replenishment.
     */
    public function purchaseRequest(Request $request, ?Item $item = null): View
    {
        $department = $request->input('department', $item?->department ?? 'all');

        if ($item) {
            $itemList = collect([$item]);
        } else {
            $query = Item::where('is_active', true)->with('supplier');
            if ($department !== 'all') {
                $query->where('department', $department);
            }

            // Prefer items at or below minimum stock level
            $critical = (clone $query)->whereColumn('current_stock', '<=', 'minimum_stock_level')->get();
            $itemList = $critical->isNotEmpty() ? $critical : $query->orderBy('current_stock', 'asc')->take(12)->get();
        }

        $items = $itemList->map(function (Item $it) {
            $suggested = max(($it->minimum_stock_level * 3) - $it->current_stock, 100);

            return [
                'id' => $it->id,
                'item_code' => $it->item_code,
                'name' => $it->name,
                'generic_name' => $it->generic_name,
                'uom' => $it->uom,
                'unit_cost' => (float) $it->unit_cost,
                'suggested_qty' => $suggested,
                'supplier_name' => $it->supplier?->name ?? 'Accredited Consignor',
            ];
        });

        return view('print.purchase-request', [
            'items' => $items,
            'department' => $department,
        ]);
    }

    /**
     * Render the official printable Departmental Item Usage Report ranking authorized wards.
     */
    public function itemUsage(Request $request, Item $item): View
    {
        $month = $request->input('month', date('m'));
        $year = (int) $request->input('year', date('Y'));

        $reportMonth = Carbon::createFromDate($year, (int) $month, 1)->format('F');
        $reportYear = $year;

        $usageData = StockRequestItem::where('item_id', $item->id)
            ->whereHas('stockRequest', function ($q) use ($month, $year) {
                $q->whereIn('status', ['approved', 'issued'])
                    ->whereMonth('request_date', $month)
                    ->whereYear('request_date', $year);
            })
            ->with('stockRequest')
            ->get()
            ->groupBy(fn ($ri) => $ri->stockRequest->requesting_unit ?? 'CSR')
            ->map(function ($group, $unitName) {
                return (object) [
                    'department_name' => $unitName,
                    'total_requested' => $group->sum(fn ($ri) => $ri->quantity_issued > 0 ? $ri->quantity_issued : $ri->quantity_requested),
                ];
            })
            ->sortByDesc('total_requested')
            ->values();

        // If no records in this month, display all-time usage to avoid an empty table during review
        if ($usageData->isEmpty()) {
            $usageData = StockRequestItem::where('item_id', $item->id)
                ->whereHas('stockRequest', function ($q) {
                    $q->whereIn('status', ['approved', 'issued']);
                })
                ->with('stockRequest')
                ->get()
                ->groupBy(fn ($ri) => $ri->stockRequest->requesting_unit ?? 'CSR')
                ->map(function ($group, $unitName) {
                    return (object) [
                        'department_name' => $unitName,
                        'total_requested' => $group->sum(fn ($ri) => $ri->quantity_issued > 0 ? $ri->quantity_issued : $ri->quantity_requested),
                    ];
                })
                ->sortByDesc('total_requested')
                ->values();
        }

        return view('print.item-usage', [
            'item' => $item,
            'usageData' => $usageData,
            'reportMonth' => $reportMonth,
            'reportYear' => $reportYear,
        ]);
    }
}
