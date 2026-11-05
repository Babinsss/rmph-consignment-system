<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'department',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return (bool) $this->is_active;
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isPharmacyStaff(): bool
    {
        return $this->role === 'pharmacy_staff';
    }

    public function isMedicalSupplyStaff(): bool
    {
        return $this->role === 'medical_supply_staff';
    }

    public function isRequester(): bool
    {
        return $this->role === 'department_requester';
    }

    public function canAccessDepartment(string $dept): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        if ($dept === 'pharmacy') {
            return $this->isPharmacyStaff();
        }

        if ($dept === 'medical_supplies') {
            return $this->isMedicalSupplyStaff();
        }

        return false;
    }

    public function deliveriesReceived(): HasMany
    {
        return $this->hasMany(Delivery::class, 'received_by_id');
    }

    public function stockRequests(): HasMany
    {
        return $this->hasMany(StockRequest::class, 'requester_id');
    }

    public function stockRequestsApproved(): HasMany
    {
        return $this->hasMany(StockRequest::class, 'approved_by_id');
    }

    public function stockRequestsIssued(): HasMany
    {
        return $this->hasMany(StockRequest::class, 'issued_by_id');
    }
}
