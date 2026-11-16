<?php

namespace App\Models;

use App\Enums\RequestStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Request extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_number',
        'unit_id',
        'requested_by_id',
        'request_date',
        'is_urgent',
        'is_downtime',
        'downtime_reference',
        'urgent_reason',
        'status',
        'rejection_reason',
        'notes',
        'fulfilled_by_id',
        'fulfilled_at',
    ];

    protected function casts(): array
    {
        return [
            'request_date' => 'datetime',
            'is_urgent' => 'boolean',
            'is_downtime' => 'boolean',
            'status' => RequestStatus::class,
            'fulfilled_at' => 'datetime',
        ];
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by_id');
    }

    public function fulfilledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'fulfilled_by_id');
    }

    public function lines(): HasMany
    {
        return $this->hasMany(RequestLine::class);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', RequestStatus::Pending);
    }

    public function scopeUrgent(Builder $query): Builder
    {
        return $query->where('is_urgent', true);
    }

    public function scopeForUnit(Builder $query, int $unitId): Builder
    {
        return $query->where('unit_id', $unitId);
    }

    public function isPending(): bool
    {
        return $this->status === RequestStatus::Pending;
    }

    public function isFulfilled(): bool
    {
        return $this->status === RequestStatus::Fulfilled;
    }

    public function isPartiallyFulfilled(): bool
    {
        return $this->status === RequestStatus::PartiallyFulfilled;
    }

    public function isRejected(): bool
    {
        return $this->status === RequestStatus::Rejected;
    }

    public function isCancelled(): bool
    {
        return $this->status === RequestStatus::Cancelled;
    }

    public function getIssuedMovements(): Collection
    {
        return StockMovement::with(['batch.item', 'user'])
            ->where('type', StockMovement::TYPE_ISSUE)
            ->where('reference_number', $this->request_number)
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function getTotalRequestedAttribute(): int
    {
        return (int) $this->lines->sum('quantity_requested');
    }

    public function getTotalIssuedAttribute(): int
    {
        return (int) $this->lines->sum('quantity_issued');
    }
}
