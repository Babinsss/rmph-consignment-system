<?php

namespace App\Models;

use App\Enums\ItemCategory;
use Carbon\Carbon;
use Database\Factories\ItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    /** @use HasFactory<ItemFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'code',
        'description',
        'category',
        'unit_of_measure',
        'consignor_id',
        'minimum_level',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'category' => ItemCategory::class,
            'minimum_level' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the consignor that supplies this item.
     */
    public function consignor(): BelongsTo
    {
        return $this->belongsTo(Consignor::class);
    }

    public function batches(): HasMany
    {
        return $this->hasMany(Batch::class);
    }

    public function activeBatches(): HasMany
    {
        return $this->hasMany(Batch::class)
            ->where('quantity_remaining', '>', 0)
            ->where('is_active', true);
    }

    /**
     * Active batches that have not expired and can be issued via FEFO.
     */
    public function issuableBatches(): HasMany
    {
        return $this->activeBatches()
            ->where('expiration_date', '>=', Carbon::today('Asia/Manila')->toDateString());
    }

    public function getCurrentStockAttribute(): int
    {
        if (array_key_exists('issuable_stock_sum', $this->attributes)) {
            return (int) ($this->attributes['issuable_stock_sum'] ?? 0);
        }

        return (int) $this->issuableBatches()->sum('quantity_remaining');
    }

    public function getExpiredStockAttribute(): int
    {
        if (array_key_exists('expired_stock_sum', $this->attributes)) {
            return (int) ($this->attributes['expired_stock_sum'] ?? 0);
        }

        return (int) $this->activeBatches()
            ->where('expiration_date', '<', Carbon::today('Asia/Manila')->toDateString())
            ->sum('quantity_remaining');
    }

    public function getIsLowStockAttribute(): bool
    {
        return $this->current_stock <= $this->minimum_level;
    }

    /**
     * Check if the item is a medical supply.
     */
    public function isSupply(): bool
    {
        return $this->category === ItemCategory::Supply;
    }

    /**
     * Check if the item is a drug/medicine.
     */
    public function isDrug(): bool
    {
        return $this->category === ItemCategory::Drug;
    }
}
