<?php

namespace Tests\Feature;

use App\Models\Unit;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Database\Seeders\UnitSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationAndRolesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(UnitSeeder::class);
        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_login_screens_are_accessible(): void
    {
        $this->get('/admin/login')->assertStatus(200);
        $this->get('/portal/login')->assertStatus(200);
    }

    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
        $this->get('/portal')->assertRedirect('/portal/login');
    }

    public function test_admin_user_can_access_admin_panel(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)->get('/admin')->assertStatus(200);
    }

    public function test_custodian_can_access_admin_panel(): void
    {
        $custodian = User::factory()->create();
        $custodian->assignRole('custodian');

        $this->actingAs($custodian)->get('/admin')->assertStatus(200);
    }

    public function test_viewer_chief_can_access_admin_panel(): void
    {
        $chief = User::factory()->create();
        $chief->assignRole('viewer');

        $this->actingAs($chief)->get('/admin')->assertStatus(200);
    }

    public function test_requesting_unit_staff_cannot_access_admin_panel(): void
    {
        $unit = Unit::where('code', 'CSR')->first();
        $nurse = User::factory()->create(['unit_id' => $unit->id]);
        $nurse->assignRole('requesting_unit');

        $this->actingAs($nurse)->get('/admin')->assertStatus(403);
    }

    public function test_requesting_unit_staff_can_access_portal_panel(): void
    {
        $unit = Unit::where('code', 'CSR')->first();
        $nurse = User::factory()->create(['unit_id' => $unit->id]);
        $nurse->assignRole('requesting_unit');

        $this->actingAs($nurse)->get('/portal')->assertStatus(200);
    }

    public function test_inactive_users_cannot_access_any_panel(): void
    {
        $inactiveAdmin = User::factory()->inactive()->create();
        $inactiveAdmin->assignRole('admin');

        $this->actingAs($inactiveAdmin)->get('/admin')->assertStatus(403);
        $this->actingAs($inactiveAdmin)->get('/portal')->assertStatus(403);
    }

    public function test_unit_cost_visibility_rules(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $custodian = User::factory()->create();
        $custodian->assignRole('custodian');

        $viewer = User::factory()->create();
        $viewer->assignRole('viewer');

        $nurse = User::factory()->create();
        $nurse->assignRole('requesting_unit');

        $this->assertTrue($admin->canViewUnitCosts());
        $this->assertTrue($custodian->canViewUnitCosts());
        $this->assertTrue($viewer->canViewUnitCosts());
        $this->assertFalse($nurse->canViewUnitCosts());
    }

    public function test_only_admin_can_access_user_management(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $custodian = User::factory()->create();
        $custodian->assignRole('custodian');

        $this->actingAs($admin)->get('/admin/users')->assertStatus(200);
        $this->actingAs($custodian)->get('/admin/users')->assertStatus(403);
    }

    public function test_admin_and_custodian_can_access_units_management(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $custodian = User::factory()->create();
        $custodian->assignRole('custodian');

        $unit = Unit::where('code', 'CSR')->first();
        $nurse = User::factory()->create(['unit_id' => $unit->id]);
        $nurse->assignRole('requesting_unit');

        $this->actingAs($admin)->get('/admin/units')->assertStatus(200);
        $this->actingAs($custodian)->get('/admin/units')->assertStatus(200);
        $this->actingAs($nurse)->get('/admin/units')->assertStatus(403);
    }

    public function test_all_six_hospital_units_and_sample_users_are_seeded(): void
    {
        $this->seed(UserSeeder::class);

        $expectedUnitCodes = ['CSR', 'HDU', 'DIALYSIS', 'PHARM_MAIN', 'PHARM_OPD', 'YAKAP'];
        foreach ($expectedUnitCodes as $code) {
            $this->assertDatabaseHas('units', ['code' => $code]);
        }

        $this->assertDatabaseHas('users', ['email' => 'admin@rmph.local']);
        $this->assertDatabaseHas('users', ['email' => 'aliana.dula@rmph.local']);
        $this->assertDatabaseHas('users', ['email' => 'kristine.batan@rmph.local']);
        $this->assertDatabaseHas('users', ['email' => 'chief@rmph.local']);
        $this->assertDatabaseHas('users', ['email' => 'csr@rmph.local']);
    }
}
