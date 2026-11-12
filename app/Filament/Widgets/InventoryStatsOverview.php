<?php

namespace App\Filament\Widgets;

use App\Enums\ItemCategory;
use App\Models\Batch;
use App\Models\Consignor;
use App\Models\Delivery;
use App\Models\Item;
use App\Support\CustodianScope;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class InventoryStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $user = auth()->user();
        $scopedCategory = $user?->custodianCategory();

        $itemsQuery = CustodianScope::applyToItems(Item::query());
        $batchesQuery = CustodianScope::applyToBatches(Batch::query());
        $deliveriesQuery = CustodianScope::applyToDeliveries(Delivery::query());

        $totalItems = $itemsQuery->count();
        $totalDeliveries = $deliveriesQuery->count();
        $totalStockOnHand = (clone $batchesQuery)->where('is_active', true)->sum('quantity_remaining');
        $activeBatchesCount = (clone $batchesQuery)->where('quantity_remaining', '>', 0)->count();

        $catalogDescription = match ($scopedCategory) {
            ItemCategory::Supply => "{$totalItems} Medical Supplies Catalog Items",
            ItemCategory::Drug => "{$totalItems} Drugs & Medicine Catalog Items",
            default => Item::where('category', ItemCategory::Supply)->count().' Supplies • '.Item::where('category', ItemCategory::Drug)->count().' Drugs',
        };

        $stockDescription = $scopedCategory
            ? "{$activeBatchesCount} Active {$scopedCategory->getLabel()} Batches"
            : "{$activeBatchesCount} Active Batches in Central Storage";

        $activeConsignors = Consignor::where('is_active', true)->count();

        return [
            Stat::make('Depot Stock on Hand', number_format($totalStockOnHand).' units')
                ->description($stockDescription)
                ->descriptionIcon('heroicon-m-circle-stack')
                ->color('success'),

            Stat::make('Deliveries (DRs) Received', (string) $totalDeliveries)
                ->description('Consignment delivery receipts recorded')
                ->descriptionIcon('heroicon-m-truck')
                ->color('teal'),

            Stat::make('Consigned Items Catalog', (string) $totalItems)
                ->description($catalogDescription)
                ->descriptionIcon('heroicon-m-archive-box')
                ->color('teal'),

            Stat::make('Accredited Consignors', (string) $activeConsignors)
                ->description('Verified hospital suppliers')
                ->descriptionIcon('heroicon-m-building-storefront')
                ->color('teal'),
        ];
    }
}
