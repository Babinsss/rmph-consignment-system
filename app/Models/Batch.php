<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Batch extends Model
{
    use HasFactory;

    protected $fillable = [
        'delivery_id',
        'item_id',
        'lot_number',
        'expiration_date',
        'quantity_received',
        'quantity_remaining',
        'unit_cost',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'expiration_date' => 'date',
            'quantity_received' => 'integer',
            'quantity_remaining' => 'integer',
            'unit_cost' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function delivery(): BelongsTo
    {
        return $this->belongsTo(Delivery::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('quantity_remaining', '>', 0)
            ->where('is_active', true);
    }

    public function scopeFefo(Builder $query): Builder
    {
        return $query->orderBy('expiration_date', 'asc');
    }

    public function scopeExpiringSoon(Builder $query, int $days = 90): Builder
    {
        $today = Carbon::today('Asia/Manila');

        return $query->whereBetween('expiration_date', [$today, $today->copy()->addDays($days)]);
    }

    public function scopeExpired(Builder $query): Builder
    {
        return $query->where('expiration_date', '<', Carbon::today('Asia/Manila'));
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->expiration_date->lt(Carbon::today('Asia/Manila'));
    }

    public function getIsExpiringSoonAttribute(): bool
    {
        $today = Carbon::today('Asia/Manila');

        return ! $this->is_expired && $this->expiration_date->lte($today->copy()->addDays(90));
    }

    public function getLineTotalAttribute(): float
    {
        return (float) ($this->quantity_received * (float) $this->unit_cost);
    }

    public function getRemainingTotalAttribute(): float
    {
        return (float) ($this->quantity_remaining * (float) $this->unit_cost);
    }
}
