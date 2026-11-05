<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class StockMovement extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'item_id',
        'delivery_item_id',
        'department',
        'movement_type',
        'reference_number',
        'reference_type',
        'reference_id',
        'source_destination',
        'lot_number',
        'expiry_date',
        'quantity_in',
        'quantity_out',
        'balance_after',
        'unit_cost',
        'total_cost',
        'user_id',
        'remarks',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'expiry_date' => 'date',
            'quantity_in' => 'integer',
            'quantity_out' => 'integer',
            'balance_after' => 'integer',
            'unit_cost' => 'decimal:2',
            'total_cost' => 'decimal:2',
        ];
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function deliveryItem(): BelongsTo
    {
        return $this->belongsTo(DeliveryItem::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }
}
