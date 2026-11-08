<?php

namespace App\Models;

use App\Enums\ItemCategory;
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

    public function getCurrentStockAttribute(): int
    {
        return (int) $this->batches()->sum('quantity_remaining');
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
