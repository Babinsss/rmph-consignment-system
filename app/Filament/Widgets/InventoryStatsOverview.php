<?php

namespace App\Filament\Widgets;

use App\Enums\ItemCategory;
use App\Enums\RequestStatus;
use App\Models\Batch;
use App\Models\Consignor;
use App\Models\Delivery;
use App\Models\Item;
use App\Models\Request;
use App\Support\CustodianScope;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class InventoryStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        $user = auth()->user();
        $scopedCategory = $user?->custodianCategory();
        $today = Carbon::today('Asia/Manila')->toDateString();
        $nearExpiryCutoff = Carbon::today('Asia/Manila')->addDays(90)->toDateString();

        $itemsQuery = CustodianScope::applyToItems(Item::query());
        $batchesQuery = CustodianScope::applyToBatches(Batch::query());
        $deliveriesQuery = CustodianScope::applyToDeliveries(Delivery::query());
        $requestsQuery = CustodianScope::applyToRequests(Request::query());

        $totalItems = $itemsQuery->count();
        $totalDeliveries = $deliveriesQuery->count();
        $totalStockOnHand = (clone $batchesQuery)
            ->where('is_active', true)
            ->where('quantity_remaining', '>', 0)
            ->whereDate('expiration_date', '>=', $today)
            ->sum('quantity_remaining');
        $activeBatchesCount = (clone $batchesQuery)
            ->where('is_active', true)
            ->where('quantity_remaining', '>', 0)
            ->whereDate('expiration_date', '>=', $today)
            ->count();

        $pendingRequestsCount = (clone $requestsQuery)
            ->whereIn('status', [RequestStatus::Pending, RequestStatus::PartiallyFulfilled])
            ->count();
        $pendingUrgentCount = (clone $requestsQuery)
            ->where('status', RequestStatus::Pending)
            ->where('is_urgent', true)
            ->count();

        $lowStockCount = (clone $itemsQuery)
            ->where('is_active', true)
            ->whereRaw(
                '(SELECT COALESCE(SUM(b.quantity_remaining), 0) FROM batches b WHERE b.item_id = items.id AND b.is_active = 1 AND b.quantity_remaining > 0 AND b.expiration_date >= ?) <= items.minimum_level',
                [$today]
            )
            ->count();

        $nearExpiryCount = (clone $batchesQuery)
            ->where('is_active', true)
            ->where('quantity_remaining', '>', 0)
            ->whereDate('expiration_date', '>=', $today)
            ->whereDate('expiration_date', '<=', $nearExpiryCutoff)
            ->count();
        $expiredStockBatches = (clone $batchesQuery)
            ->where('is_active', true)
            ->where('quantity_remaining', '>', 0)
            ->whereDate('expiration_date', '<', $today)
            ->count();

        $catalogDescription = match ($scopedCategory) {
            ItemCategory::Supply => "{$totalItems} Medical Supplies Catalog Items",
            ItemCategory::Drug => "{$totalItems} Drugs & Medicine Catalog Items",
            default => Item::where('category', ItemCategory::Supply)->count().' Supplies • '.Item::where('category', ItemCategory::Drug)->count().' Drugs',
        };

        $stockDescription = $scopedCategory
            ? "{$activeBatchesCount} Issuable {$scopedCategory->getLabel()} Batches"
            : "{$activeBatchesCount} Issuable Batches in Central Storage";

        $activeConsignors = Consignor::where('is_active', true)->count();

        return [
            Stat::make('Issuable Stock on Hand', number_format($totalStockOnHand).' units')
                ->description($stockDescription)
                ->descriptionIcon('heroicon-m-circle-stack')
                ->color('success'),

            Stat::make('Pending Requisitions', (string) $pendingRequestsCount)
                ->description($pendingUrgentCount > 0 ? "{$pendingUrgentCount} URGENT (STAT) awaiting action" : 'Open & partially fulfilled requests')
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->color($pendingUrgentCount > 0 ? 'danger' : ($pendingRequestsCount > 0 ? 'warning' : 'teal')),

            Stat::make('Low Stock Alerts', (string) $lowStockCount)
                ->description('Active catalog items at or below minimum level')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($lowStockCount > 0 ? 'warning' : 'success'),

            Stat::make('Near Expiry (<= 90d)', (string) $nearExpiryCount)
                ->description($expiredStockBatches > 0 ? "{$expiredStockBatches} expired batch(es) quarantined from issue" : 'Batches expiring within 90 days')
                ->descriptionIcon('heroicon-m-clock')
                ->color($expiredStockBatches > 0 ? 'danger' : ($nearExpiryCount > 0 ? 'warning' : 'success')),

            Stat::make('Deliveries (DRs) Received', (string) $totalDeliveries)
                ->description($catalogDescription)
                ->descriptionIcon('heroicon-m-truck')
                ->color('teal'),

            Stat::make('Accredited Consignors', (string) $activeConsignors)
                ->description('Verified hospital suppliers')
                ->descriptionIcon('heroicon-m-building-storefront')
                ->color('teal'),
        ];
    }
}
