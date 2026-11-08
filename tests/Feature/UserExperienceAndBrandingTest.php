<?php

namespace Tests\Feature;

use App\Enums\ItemCategory;
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

    public function test_admin_login_page_renders_hospital_branding_and_demo_accounts(): void
    {
        $response = $this->get('/admin/login');

        $response->assertStatus(200);
        $response->assertSee('Roxas Memorial Provincial Hospital');
        $response->assertSee('Central Operations');
        $response->assertSee('Custodian Management');
        $response->assertSee('Demo Accounts (Click to Fill)');
        $response->assertSee('admin@rmph.local');
        $response->assertSee('Open Ward Portal');
    }

    public function test_portal_login_page_renders_ward_portal_branding_and_demo_accounts(): void
    {
        $response = $this->get('/portal/login');

        $response->assertStatus(200);
        $response->assertSee('Roxas Memorial Provincial Hospital');
        $response->assertSee('Ward Requisition Portal');
        $response->assertSee('Ward Staff Demo Accounts (Click to Fill)');
        $response->assertSee('csr@rmph.local');
        $response->assertSee('Open Central Operations');
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
}
