<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockRequest extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The authorized hospital departments eligible to request consignment stocks.
     *
     * @var array<string, string>
     */
    public const ALLOWED_REQUESTING_UNITS = [
        'CSR' => 'CSR (Central Supply Room)',
        'HDU' => 'HDU (High Dependency Unit)',
        'GUGMA DIALYSIS CENTER' => 'Gugma Dialysis Center',
        'PHARMACY MAIN' => 'Pharmacy Main',
        'PHARMACY OPD' => 'Pharmacy OPD',
        'YAKAP' => 'Yakap',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'request_number',
        'department',
        'requesting_unit',
        'requester_id',
        'requester_name',
        'request_date',
        'status',
        'approved_by_id',
        'approved_at',
        'issued_by_id',
        'issued_at',
        'purpose',
        'patient_mrn',
        'notes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'request_date' => 'date',
            'approved_at' => 'datetime',
            'issued_at' => 'datetime',
        ];
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_id');
    }

    public function issuedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(StockRequestItem::class);
    }

    public function movements(): MorphMany
    {
        return $this->morphMany(StockMovement::class, 'reference');
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
