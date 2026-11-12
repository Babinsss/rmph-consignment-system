<?php

namespace App\Support;

use App\Enums\ItemCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

/**
 * CustodianScope provides reusable Eloquent query constraints to restrict
 * data visibility for category-scoped custodians:
 *
 *   - custodian_supplies  → sees only Medical Supply items/batches/deliveries
 *   - custodian_drugs     → sees only Drugs & Medicine items/batches/deliveries
 *   - admin / custodian / viewer → sees everything (no restriction)
 */
class CustodianScope
{
    /**
     * Restrict an Items query to the authenticated custodian's category.
     * No-op for roles with full access.
     */
    public static function applyToItems(Builder $query, ?User $user = null): Builder
    {
        $user ??= auth()->user();
        $category = $user?->custodianCategory();

        if ($category instanceof ItemCategory) {
            $query->where('category', $category->value);
        }

        return $query;
    }

    /**
     * Restrict a Batches query through the item category.
     * No-op for roles with full access.
     */
    public static function applyToBatches(Builder $query, ?User $user = null): Builder
    {
        $user ??= auth()->user();
        $category = $user?->custodianCategory();

        if ($category instanceof ItemCategory) {
            $query->whereHas('item', fn (Builder $q) => $q->where('category', $category->value));
        }

        return $query;
    }

    /**
     * Restrict a Deliveries query so only deliveries that contain at least one
     * batch of the custodian's category are shown.
     * No-op for roles with full access.
     */
    public static function applyToDeliveries(Builder $query, ?User $user = null): Builder
    {
        $user ??= auth()->user();
        $category = $user?->custodianCategory();

        if ($category instanceof ItemCategory) {
            $query->whereHas('batches.item', fn (Builder $q) => $q->where('category', $category->value));
        }

        return $query;
    }

    /**
     * Restrict a StockMovements query through the batch → item category.
     * No-op for roles with full access.
     */
    public static function applyToStockMovements(Builder $query, ?User $user = null): Builder
    {
        $user ??= auth()->user();
        $category = $user?->custodianCategory();

        if ($category instanceof ItemCategory) {
            $query->whereHas('batch.item', fn (Builder $q) => $q->where('category', $category->value));
        }

        return $query;
    }

    /**
     * Restrict a Requests query so only requests that contain at least one line
     * item in the custodian's category are shown.
     * No-op for roles with full access.
     */
    public static function applyToRequests(Builder $query, ?User $user = null): Builder
    {
        $user ??= auth()->user();
        $category = $user?->custodianCategory();

        if ($category instanceof ItemCategory) {
            $query->whereHas('lines.item', fn (Builder $q) => $q->where('category', $category->value));
        }

        return $query;
    }
}
