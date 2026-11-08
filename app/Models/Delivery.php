<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Delivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'consignor_id',
        'dr_number',
        'delivery_date',
        'received_by_id',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'delivery_date' => 'date',
        ];
    }

    public function consignor(): BelongsTo
    {
        return $this->belongsTo(Consignor::class);
    }

    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by_id');
    }

    public function batches(): HasMany
    {
        return $this->hasMany(Batch::class);
    }

    public function getTotalAmountAttribute(): float
    {
        return (float) $this->batches->sum(fn (Batch $batch) => $batch->quantity_received * (float) $batch->unit_cost);
    }

    public function getTotalUnitsAttribute(): int
    {
        return (int) $this->batches->sum('quantity_received');
    }
}
