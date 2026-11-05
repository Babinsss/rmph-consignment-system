<?php

namespace App\Filament\Pages;

use App\Models\StockMovement;
use App\Models\Supplier;
use BackedEnum;
use Carbon\Carbon;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;
use UnitEnum;

class StatementOfConsumedGoods extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentCurrencyDollar;

    protected static string|UnitEnum|null $navigationGroup = 'Reports & Stockcards';

    protected static ?string $title = 'Statement of Consumed Goods (StCG)';

    protected static ?string $navigationLabel = 'Supplier StCG Liquidation';

    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.pages.statement-of-consumed-goods';

    public ?int $supplierId = null;

    public string $preset = 'this_month';

    public ?string $dateFrom = null;

    public ?string $dateTo = null;

    public string $stcgNumber = '';

    public function mount(): void
    {
        $firstSupplier = Supplier::where('is_active', true)->orderBy('name')->first();
        $this->supplierId = $firstSupplier?->id;
        $this->applyPreset();
        $this->generateStcgNumber();
    }

    public function updatedPreset(): void
    {
        $this->applyPreset();
        $this->generateStcgNumber();
    }

    public function updatedSupplierId(): void
    {
        $this->generateStcgNumber();
    }

    public function applyPreset(): void
    {
        $now = Carbon::now();

        switch ($this->preset) {
            case 'last_month':
                $this->dateFrom = $now->copy()->subMonth()->startOfMonth()->format('Y-m-d');
                $this->dateTo = $now->copy()->subMonth()->endOfMonth()->format('Y-m-d');
                break;
            case 'this_month':
            default:
                $this->dateFrom = $now->copy()->startOfMonth()->format('Y-m-d');
                $this->dateTo = $now->copy()->endOfMonth()->format('Y-m-d');
                break;
        }
    }

    public function generateStcgNumber(): void
    {
        $supplier = Supplier::find($this->supplierId);
        $code = $supplier?->code ?? 'SUP';
        $periodTag = Carbon::parse($this->dateFrom)->format('Ym');
        $this->stcgNumber = "StCG-{$periodTag}-{$code}";
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function getConsumedItems(): Collection
    {
        if (! $this->supplierId) {
            return collect();
        }

        $dateFrom = Carbon::parse($this->dateFrom)->startOfDay();
        $dateTo = Carbon::parse($this->dateTo)->endOfDay();

        // Get all outward movements for items belonging to this supplier
        $movements = StockMovement::whereHas('item', function ($q) {
            $q->where('supplier_id', $this->supplierId);
        })
            ->where('movement_type', 'REQUEST_OUT')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->with('item')
            ->get();

        // Group by item
        return $movements->groupBy('item_id')->map(function ($itemMovements) {
            $first = $itemMovements->first();
            $item = $first->item;
            $totalQty = $itemMovements->sum('quantity_out');
            $unitCost = (float) $item->unit_cost;
            $totalCost = $totalQty * $unitCost;

            return [
                'item_id' => $item->id,
                'item_code' => $item->item_code,
                'item_name' => $item->name,
                'generic_name' => $item->generic_name,
                'category' => $item->category,
                'uom' => $item->uom,
                'unit_cost' => $unitCost,
                'selling_price' => (float) $item->selling_price,
                'quantity_consumed' => $totalQty,
                'total_amount' => $totalCost,
                'movement_count' => $itemMovements->count(),
            ];
        })->values();
    }

    public function exportCsv(): StreamedResponse
    {
        $supplier = Supplier::find($this->supplierId);
        $rows = $this->getConsumedItems();
        $fileName = "StCG-{$this->stcgNumber}-".now()->format('Ymd').'.csv';

        return response()->streamDownload(function () use ($supplier, $rows): void {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, ['ROXAS MEMORIAL PROVINCIAL HOSPITAL']);
            fputcsv($handle, ['STATEMENT OF CONSUMED GOODS (StCG)']);
            fputcsv($handle, ['StCG No:', $this->stcgNumber]);
            fputcsv($handle, ['Consignor:', $supplier?->name ?? 'N/A']);
            fputcsv($handle, ['Period:', "{$this->dateFrom} to {$this->dateTo}"]);
            fputcsv($handle, []);

            fputcsv($handle, [
                'Item Code',
                'Item Description',
                'Category',
                'UOM',
                'Quantity Consumed',
                'Consignor Unit Price (PHP)',
                'Total Amount Payable (PHP)',
            ]);

            $totalAmount = 0.0;
            foreach ($rows as $row) {
                $totalAmount += $row['total_amount'];
                fputcsv($handle, [
                    $row['item_code'],
                    $row['item_name'],
                    $row['category'],
                    $row['uom'],
                    $row['quantity_consumed'],
                    number_format($row['unit_cost'], 2, '.', ''),
                    number_format($row['total_amount'], 2, '.', ''),
                ]);
            }

            fputcsv($handle, []);
            fputcsv($handle, ['', '', '', 'TOTAL LIQUIDATION AMOUNT (PHP):', '', '', number_format($totalAmount, 2, '.', '')]);

            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();
        $currentSupplier = Supplier::find($this->supplierId);
        $consumedRows = $this->getConsumedItems();
        $totalPayable = $consumedRows->sum('total_amount');
        $totalQuantity = $consumedRows->sum('quantity_consumed');

        return [
            'suppliers' => $suppliers,
            'currentSupplier' => $currentSupplier,
            'consumedRows' => $consumedRows,
            'totalPayable' => $totalPayable,
            'totalQuantity' => $totalQuantity,
        ];
    }
}
