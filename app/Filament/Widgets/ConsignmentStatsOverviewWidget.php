<?php

namespace App\Filament\Widgets;

use App\Models\DeliveryItem;
use App\Models\Item;
use App\Models\StockRequest;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class ConsignmentStatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $user = Auth::user();

        $itemsQuery = Item::query()->where('is_active', true);
        $batchesQuery = DeliveryItem::query()->where('status', 'available');
        $requestsQuery = StockRequest::query()->whereIn('status', ['pending', 'approved']);

        if ($user && $user->isPharmacyStaff()) {
            $itemsQuery->where('department', 'pharmacy');
            $batchesQuery->whereHas('item', fn ($q) => $q->where('department', 'pharmacy'));
            $requestsQuery->where('department', 'pharmacy');
        } elseif ($user && $user->isMedicalSupplyStaff()) {
            $itemsQuery->where('department', 'medical_supplies');
            $batchesQuery->whereHas('item', fn ($q) => $q->where('department', 'medical_supplies'));
            $requestsQuery->where('department', 'medical_supplies');
        }

        $totalValuation = (float) $batchesQuery->selectRaw('SUM(quantity_remaining * unit_cost) as total')->value('total') ?? 0.0;
        $activeItemsCount = $itemsQuery->count();
        $pendingRequestsCount = $requestsQuery->count();

        // Calculate low stock items count
        $lowStockCount = 0;
        $items = (clone $itemsQuery)->with('deliveryItems')->get();
        foreach ($items as $item) {
            if ($item->current_stock <= $item->minimum_stock_level) {
                $lowStockCount++;
            }
        }

        return [
            Stat::make('Consignment Stock Value', '₱'.number_format($totalValuation, 2))
                ->description('Total consigned goods on hand')
                ->descriptionIcon(Heroicon::OutlinedBanknotes)
                ->chart([24000, 28000, 31000, 35000, 38000, 42000, (int) $totalValuation])
                ->color('success'),

            Stat::make('Active Catalog Items', (string) $activeItemsCount)
                ->description('Monitored healthcare supplies')
                ->descriptionIcon(Heroicon::OutlinedArchiveBox)
                ->chart([4, 4, 5, 5, 6, 6, $activeItemsCount])
                ->color('info'),

            Stat::make('Actionable Stock Requests', (string) $pendingRequestsCount)
                ->description('Pending review / ready for issuance')
                ->descriptionIcon(Heroicon::OutlinedClipboardDocumentList)
                ->chart([1, 2, 0, 3, 2, 1, $pendingRequestsCount])
                ->color($pendingRequestsCount > 0 ? 'warning' : 'gray'),

            Stat::make('Low Stock Alerts', (string) $lowStockCount)
                ->description('At or below reorder threshold')
                ->descriptionIcon(Heroicon::OutlinedExclamationTriangle)
                ->chart([3, 2, 1, 0, 1, 1, $lowStockCount])
                ->color($lowStockCount > 0 ? 'danger' : 'success'),
        ];
    }
}
