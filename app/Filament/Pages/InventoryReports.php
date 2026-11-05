<?php

namespace App\Filament\Pages;

use App\Models\Item;
use App\Models\StockMovement;
use App\Models\Supplier;
use BackedEnum;
use Carbon\Carbon;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;
use UnitEnum;

class InventoryReports extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static string|UnitEnum|null $navigationGroup = 'Reports & Stockcards';

    protected static ?int $navigationSort = 1;

    protected static ?string $title = 'Consignment Inventory Reports';

    protected string $view = 'filament.pages.inventory-reports';

    public string $preset = 'this_month';

    public ?string $dateFrom = null;

    public ?string $dateTo = null;

    public string $department = 'all';

    public string $supplierId = 'all';

    public function mount(): void
    {
        $user = Auth::user();
        if ($user && $user->isPharmacyStaff()) {
            $this->department = 'pharmacy';
        } elseif ($user && $user->isMedicalSupplyStaff()) {
            $this->department = 'medical_supplies';
        }

        $this->applyPreset();
    }

    public function updatedPreset(): void
    {
        $this->applyPreset();
    }

    public function applyPreset(): void
    {
        $now = Carbon::now();

        switch ($this->preset) {
            case 'this_week':
                $this->dateFrom = $now->copy()->startOfWeek()->format('Y-m-d');
                $this->dateTo = $now->copy()->endOfWeek()->format('Y-m-d');
                break;
            case 'this_month':
                $this->dateFrom = $now->copy()->startOfMonth()->format('Y-m-d');
                $this->dateTo = $now->copy()->endOfMonth()->format('Y-m-d');
                break;
            case 'this_year':
                $this->dateFrom = $now->copy()->startOfYear()->format('Y-m-d');
                $this->dateTo = $now->copy()->endOfYear()->format('Y-m-d');
                break;
            case 'all_time':
                $this->dateFrom = Carbon::parse('2020-01-01')->format('Y-m-d');
                $this->dateTo = $now->format('Y-m-d');
                break;
            case 'custom':
            default:
                // Keep existing dateFrom and dateTo
                break;
        }
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function getReportData(): Collection
    {
        $itemsQuery = Item::query()->where('is_active', true)->with('supplier');

        if ($this->department !== 'all') {
            $itemsQuery->where('department', $this->department);
        }

        if ($this->supplierId !== 'all') {
            $itemsQuery->where('supplier_id', $this->supplierId);
        }

        $items = $itemsQuery->get();
        $dateFrom = Carbon::parse($this->dateFrom)->startOfDay();
        $dateTo = Carbon::parse($this->dateTo)->endOfDay();

        return $items->map(function (Item $item) use ($dateFrom, $dateTo) {
            // Beginning Balance: movements before dateFrom
            $beginningIn = StockMovement::where('item_id', $item->id)
                ->where('created_at', '<', $dateFrom)
                ->sum('quantity_in');

            $beginningOut = StockMovement::where('item_id', $item->id)
                ->where('created_at', '<', $dateFrom)
                ->sum('quantity_out');

            $beginningBalance = (int) ($beginningIn - $beginningOut);

            // Inward Received during period
            $receivedQty = (int) StockMovement::where('item_id', $item->id)
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->where('movement_type', 'DELIVERY_IN')
                ->sum('quantity_in');

            $receivedCost = (float) StockMovement::where('item_id', $item->id)
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->where('movement_type', 'DELIVERY_IN')
                ->sum('total_cost');

            // Outward Issued during period
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
                'id' => $item->id,
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
    }

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        $reportRows = $this->getReportData();

        $totalReceivedCost = $reportRows->sum('received_cost');
        $totalIssuedCost = $reportRows->sum('issued_cost');
        $totalEndingValuation = $reportRows->sum('ending_valuation');

        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();

        return [
            'reportRows' => $reportRows,
            'totalReceivedCost' => $totalReceivedCost,
            'totalIssuedCost' => $totalIssuedCost,
            'totalEndingValuation' => $totalEndingValuation,
            'suppliers' => $suppliers,
        ];
    }

    /**
     * Export the filtered periodic consignment report to CSV.
     */
    public function exportCsv(): StreamedResponse
    {
        $rows = $this->getReportData();
        $fileName = 'consignment-inventory-report-'.$this->preset.'-'.now()->format('Ymd-His').'.csv';

        return response()->streamDownload(function () use ($rows): void {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM for Microsoft Excel compatibility
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'Item Code',
                'Item Name',
                'Generic Name',
                'Department',
                'Category',
                'UOM',
                'Consignor (Supplier)',
                'Beginning Qty',
                'Received In (Qty)',
                'Received In Cost (PHP)',
                'Issued Out (Qty)',
                'Issued Out Cost (PHP)',
                'Ending Qty',
                'Unit Cost (PHP)',
                'Ending Valuation (PHP)',
            ]);

            foreach ($rows as $row) {
                fputcsv($handle, [
                    $row['item_code'],
                    $row['name'],
                    $row['generic_name'] ?? '',
                    $row['department'] === 'pharmacy' ? 'Pharmacy' : 'Medical Supplies',
                    $row['category'],
                    $row['uom'],
                    $row['supplier_name'],
                    $row['beginning_balance'],
                    $row['received_qty'],
                    number_format($row['received_cost'], 2, '.', ''),
                    $row['issued_qty'],
                    number_format($row['issued_cost'], 2, '.', ''),
                    $row['ending_balance'],
                    number_format($row['unit_cost'], 2, '.', ''),
                    number_format($row['ending_valuation'], 2, '.', ''),
                ]);
            }

            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
