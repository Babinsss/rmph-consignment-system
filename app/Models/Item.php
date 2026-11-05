<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'item_code',
        'name',
        'generic_name',
        'brand_name',
        'description',
        'department',
        'category',
        'uom',
        'supplier_id',
        'minimum_stock_level',
        'unit_cost',
        'selling_price',
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
            'minimum_stock_level' => 'integer',
            'unit_cost' => 'decimal:2',
            'selling_price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function deliveryItems(): HasMany
    {
        return $this->hasMany(DeliveryItem::class);
    }

    public function stockRequestItems(): HasMany
    {
        return $this->hasMany(StockRequestItem::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function getCurrentStockAttribute(): int
    {
        return (int) $this->deliveryItems()
            ->where('status', 'available')
            ->sum('quantity_remaining');
    }

    public function getTotalValuationAttribute(): float
    {
        return (float) $this->deliveryItems()
            ->where('status', 'available')
            ->selectRaw('SUM(quantity_remaining * unit_cost) as total')
            ->value('total') ?? 0.0;
    }

    public function getStockStatusAttribute(): string
    {
        $stock = $this->current_stock;
        if ($stock <= 0) {
            return 'out_of_stock';
        }

        if ($stock <= $this->minimum_stock_level) {
            return 'low_stock';
        }

        return 'in_stock';
    }

    public function scopePharmacy(Builder $query): Builder
    {
        return $query->where('department', 'pharmacy');
    }

    public function scopeMedicalSupplies(Builder $query): Builder
    {
        return $query->where('department', 'medical_supplies');
    }
}
