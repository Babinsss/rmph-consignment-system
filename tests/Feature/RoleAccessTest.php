<?php

namespace Tests\Feature;

use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_role_helpers_work_accurately(): void
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'department' => 'all',
            'is_active' => true,
        ]);

        $pharmacyStaff = User::create([
            'name' => 'Pharmacy Staff',
            'email' => 'pharm@test.com',
            'password' => bcrypt('password'),
            'role' => 'pharmacy_staff',
            'department' => 'pharmacy',
            'is_active' => true,
        ]);

        $medStaff = User::create([
            'name' => 'Medical Supply Staff',
            'email' => 'med@test.com',
            'password' => bcrypt('password'),
            'role' => 'medical_supply_staff',
            'department' => 'medical_supplies',
            'is_active' => true,
        ]);

        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($admin->isPharmacyStaff());
        $this->assertTrue($admin->canAccessDepartment('pharmacy'));
        $this->assertTrue($admin->canAccessDepartment('medical_supplies'));

        $this->assertTrue($pharmacyStaff->isPharmacyStaff());
        $this->assertTrue($pharmacyStaff->canAccessDepartment('pharmacy'));
        $this->assertFalse($pharmacyStaff->canAccessDepartment('medical_supplies'));

        $this->assertTrue($medStaff->isMedicalSupplyStaff());
        $this->assertTrue($medStaff->canAccessDepartment('medical_supplies'));
        $this->assertFalse($medStaff->canAccessDepartment('pharmacy'));
    }

    public function test_inactive_users_cannot_access_panel(): void
    {
        $panel = Filament::getPanel('admin');

        $activeUser = User::create([
            'name' => 'Active User',
            'email' => 'active@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'department' => 'all',
            'is_active' => true,
        ]);

        $inactiveUser = User::create([
            'name' => 'Inactive User',
            'email' => 'inactive@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'department' => 'all',
            'is_active' => false,
        ]);

        $this->assertTrue($activeUser->canAccessPanel($panel));
        $this->assertFalse($inactiveUser->canAccessPanel($panel));
    }
}
