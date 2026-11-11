<?php

namespace Tests\Feature;

use App\Enums\ItemCategory;
use App\Filament\Auth\Login;
use App\Filament\Portal\Widgets\UnitInfoWidget;
use App\Filament\Widgets\InventoryStatsOverview;
use App\Models\Consignor;
use App\Models\Item;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Database\Seeders\UnitSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class UserExperienceAndBrandingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(UnitSeeder::class);
        $this->seed(RoleAndPermissionSeeder::class);
        $this->seed(UserSeeder::class);
    }

    public function test_admin_login_page_renders_hospital_branding(): void
    {
        $response = $this->get('/admin/login');

        $response->assertStatus(200);
        $response->assertSee('Roxas Memorial Provincial Hospital');
        $response->assertSee('Consignment Inventory System');
        $response->assertSee('Authorized hospital personnel only');
        $response->assertSee('Ward staff? Open the Ward Portal');
        $response->assertDontSee('Remember me');
    }

    public function test_admin_login_page_renders_demo_accounts_in_local_environment(): void
    {
        $this->app->detectEnvironment(fn () => 'local');

        $response = $this->get('/admin/login');

        $response->assertStatus(200);
        $response->assertSee('Use a demo account');
        $response->assertSee('admin@rmph.local');
        $response->assertDontSee('Key: password');
    }

    public function test_admin_login_page_hides_demo_accounts_in_production_environment(): void
    {
        $this->app->detectEnvironment(fn () => 'production');

        $response = $this->get('/admin/login');

        $response->assertStatus(200);
        $response->assertDontSee('Use a demo account');
        $response->assertDontSee('admin@rmph.local');
    }

    public function test_admin_login_validation_failure_message(): void
    {
        Livewire::test(Login::class)
            ->set('data.email', 'wrong@rmph.local')
            ->set('data.password', 'incorrect-password')
            ->call('authenticate')
            ->assertHasErrors(['data.email' => 'The email or password is not correct.']);
    }

    public function test_portal_login_page_renders_ward_portal_branding(): void
    {
        $response = $this->get('/portal/login');

        $response->assertStatus(200);
        $response->assertSee('Roxas Memorial Provincial Hospital');
        $response->assertSee('Ward Requisition Portal');
        $response->assertSee('For CSR, HDU, Dialysis, Pharmacy and Yakap staff');
        $response->assertSee('Central Operations staff? Open the Operations login');
        $response->assertDontSee('Remember me');
    }

    public function test_portal_login_page_renders_demo_accounts_in_local_environment(): void
    {
        $this->app->detectEnvironment(fn () => 'local');

        $response = $this->get('/portal/login');

        $response->assertStatus(200);
        $response->assertSee('Use a demo account');
        $response->assertSee('csr@rmph.local');
        $response->assertDontSee('Key: password');
    }

    public function test_portal_login_page_hides_demo_accounts_in_production_environment(): void
    {
        $this->app->detectEnvironment(fn () => 'production');

        $response = $this->get('/portal/login');

        $response->assertStatus(200);
        $response->assertDontSee('Use a demo account');
        $response->assertDontSee('csr@rmph.local');
    }

    public function test_portal_login_validation_failure_message(): void
    {
        Livewire::test(\App\Filament\Portal\Auth\Login::class)
            ->set('data.email', 'wrong@rmph.local')
            ->set('data.password', 'incorrect-password')
            ->call('authenticate')
            ->assertHasErrors(['data.email' => 'The email or password is not correct.']);
    }

    public function test_inventory_stats_overview_widget_mounts_correctly(): void
    {
        $consignor = Consignor::create([
            'name' => 'Test Supplier',
            'code' => 'SUPP-TEST',
            'is_active' => true,
        ]);

        Item::create([
            'code' => 'ITEM-001',
            'description' => 'Test Medical Supply Item',
            'category' => ItemCategory::Supply,
            'unit_of_measure' => 'piece',
            'consignor_id' => $consignor->id,
            'minimum_level' => 10,
            'is_active' => true,
        ]);

        $admin = User::where('email', 'admin@rmph.local')->first();
        $this->actingAs($admin);

        Livewire::test(InventoryStatsOverview::class)
            ->assertSuccessful();
    }

    public function test_ward_unit_info_widget_mounts_and_displays_unit_data(): void
    {
        $csrStaff = User::where('email', 'csr@rmph.local')->first();
        $this->actingAs($csrStaff);

        Livewire::test(UnitInfoWidget::class)
            ->assertSuccessful()
            ->assertSee('CSR')
            ->assertSee('Central Supply Room')
            ->assertSee('CSR Ward Staff')
            ->assertSee('Browse Catalog');
    }

    public function test_design_system_page_renders_tokens_and_badges_for_admin(): void
    {
        $admin = User::where('email', 'admin@rmph.local')->first();
        $this->actingAs($admin);

        $response = $this->get('/admin/design-system');
        $response->assertStatus(200);
        $response->assertSee('HECS Design System');
        $response->assertSee('Teal 700 (#0F766E)');
        $response->assertSee('Unified Badge System');
        $response->assertSee('Pending');
        $response->assertSee('Fulfilled');
        $response->assertSee('Partly Fulfilled');
        $response->assertSee('Rejected');
        $response->assertSee('8px Spacing Grid');
        $response->assertSee('48px Minimum Tap Targets');
    }

    public function test_design_system_page_is_restricted_from_unauthorized_guests(): void
    {
        $response = $this->get('/admin/design-system');
        $response->assertRedirect('/admin/login');
    }

    public function test_ward_portal_renders_bottom_navigation_bar_with_large_targets(): void
    {
        $csrStaff = User::where('email', 'csr@rmph.local')->first();
        $this->actingAs($csrStaff);

        $response = $this->get('/portal');
        $response->assertStatus(200);
        $response->assertSee('aria-label="Ward Navigation"', false);
        $response->assertSee('Home');
        $response->assertSee('Catalog');
        $response->assertSee('Requests');
        $response->assertSee('STAT');
        $response->assertSee('min-h-[56px]', false);
    }
}
