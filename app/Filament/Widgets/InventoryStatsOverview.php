<?php

namespace App\Filament\Widgets;

use App\Enums\ItemCategory;
use App\Models\Batch;
use App\Models\Consignor;
use App\Models\Delivery;
use App\Models\Item;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class InventoryStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $suppliesCount = Item::where('category', ItemCategory::Supply)->count();
        $drugsCount = Item::where('category', ItemCategory::Drug)->count();
        $totalItems = Item::count();

        $activeConsignors = Consignor::where('is_active', true)->count();
        $totalDeliveries = Delivery::count();
        $totalStockOnHand = Batch::where('is_active', true)->sum('quantity_remaining');
        $activeBatchesCount = Batch::where('quantity_remaining', '>', 0)->count();

        return [
            Stat::make('Depot Stock on Hand', number_format($totalStockOnHand).' units')
                ->description("{$activeBatchesCount} Active Batches in Central Storage")
                ->descriptionIcon('heroicon-m-circle-stack')
                ->color('success'),

            Stat::make('Deliveries (DRs) Received', (string) $totalDeliveries)
                ->description('Consignment delivery receipts recorded')
                ->descriptionIcon('heroicon-m-truck')
                ->color('teal'),

            Stat::make('Consigned Items Catalog', (string) $totalItems)
                ->description("{$suppliesCount} Supplies • {$drugsCount} Drugs")
                ->descriptionIcon('heroicon-m-archive-box')
                ->color('teal'),

            Stat::make('Accredited Consignors', (string) $activeConsignors)
                ->description('Verified hospital suppliers')
                ->descriptionIcon('heroicon-m-building-storefront')
                ->color('teal'),
        ];
    }
}
