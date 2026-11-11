<?php

namespace Tests\Feature;

use App\Enums\ItemCategory;
use App\Filament\Resources\Items\Pages\ListItems;
use App\Models\Batch;
use App\Models\Consignor;
use App\Models\Delivery;
use App\Models\Item;
use App\Models\Unit;
use App\Models\User;
use Database\Seeders\ConsignorSeeder;
use Database\Seeders\ItemSeeder;
use Database\Seeders\RoleAndPermissionSeeder;
use Database\Seeders\UnitSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CatalogAndConsignorsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(UnitSeeder::class);
        $this->seed(RoleAndPermissionSeeder::class);
        $this->seed(ConsignorSeeder::class);
        $this->seed(ItemSeeder::class);
    }

    public function test_sixteen_consignors_are_seeded(): void
    {
        $this->assertCount(16, Consignor::all());

        $this->assertDatabaseHas('consignors', ['code' => 'ZUELLIG']);
        $this->assertDatabaseHas('consignors', ['code' => 'METRODRUG']);
        $this->assertDatabaseHas('consignors', ['code' => 'ENDURE']);
        $this->assertDatabaseHas('consignors', ['code' => 'UNILAB']);
        $this->assertDatabaseHas('consignors', ['code' => 'BBRAUN']);
        $this->assertDatabaseHas('consignors', ['code' => 'FRESENIUS']);
    }

    public function test_custodian_can_access_consignors_list(): void
    {
        $custodian = User::factory()->create();
        $custodian->assignRole('custodian');

        $this->actingAs($custodian)->get('/admin/consignors')->assertStatus(200);
    }

    public function test_viewer_chief_can_view_consignors_list(): void
    {
        $chief = User::factory()->create();
        $chief->assignRole('viewer');

        $this->actingAs($chief)->get('/admin/consignors')->assertStatus(200);
    }

    public function test_requesting_unit_cannot_access_consignors_in_admin(): void
    {
        $unit = Unit::where('code', 'CSR')->first();
        $nurse = User::factory()->create(['unit_id' => $unit->id]);
        $nurse->assignRole('requesting_unit');

        $this->actingAs($nurse)->get('/admin/consignors')->assertStatus(403);
    }

    public function test_custodian_and_viewer_can_access_item_catalog_in_admin(): void
    {
        $custodian = User::factory()->create();
        $custodian->assignRole('custodian');

        $chief = User::factory()->create();
        $chief->assignRole('viewer');

        $this->actingAs($custodian)->get('/admin/items')->assertStatus(200);
        $this->actingAs($chief)->get('/admin/items')->assertStatus(200);
    }

    public function test_requesting_unit_cannot_access_admin_items(): void
    {
        $unit = Unit::where('code', 'CSR')->first();
        $nurse = User::factory()->create(['unit_id' => $unit->id]);
        $nurse->assignRole('requesting_unit');

        $this->actingAs($nurse)->get('/admin/items')->assertStatus(403);
    }

    public function test_requesting_unit_can_browse_items_in_portal(): void
    {
        $unit = Unit::where('code', 'CSR')->first();
        $nurse = User::factory()->create(['unit_id' => $unit->id]);
        $nurse->assignRole('requesting_unit');

        $this->actingAs($nurse)->get('/portal/items')->assertStatus(200);
    }

    public function test_item_model_properties_and_helpers(): void
    {
        $consignor = Consignor::first();

        $supply = Item::create([
            'code' => 'TEST-SUP-01',
            'description' => 'Test Surgical Blade #10',
            'category' => ItemCategory::Supply,
            'unit_of_measure' => 'box of 100',
            'consignor_id' => $consignor->id,
            'minimum_level' => 15,
            'is_active' => true,
        ]);

        $drug = Item::create([
            'code' => 'TEST-DRG-01',
            'description' => 'Test Amoxicillin 500mg cap',
            'category' => ItemCategory::Drug,
            'unit_of_measure' => 'box of 100',
            'consignor_id' => $consignor->id,
            'minimum_level' => 50,
            'is_active' => true,
        ]);

        $this->assertTrue($supply->isSupply());
        $this->assertFalse($supply->isDrug());

        $this->assertTrue($drug->isDrug());
        $this->assertFalse($drug->isSupply());

        $this->assertEquals($consignor->id, $supply->consignor->id);
        $this->assertEquals(15, $supply->minimum_level);
    }

    public function test_both_supplies_and_drugs_are_seeded(): void
    {
        $suppliesCount = Item::where('category', ItemCategory::Supply)->count();
        $drugsCount = Item::where('category', ItemCategory::Drug)->count();

        $this->assertGreaterThan(0, $suppliesCount);
        $this->assertGreaterThan(0, $drugsCount);

        $this->assertDatabaseHas('items', ['code' => 'SUP-001', 'category' => 'supply']);
        $this->assertDatabaseHas('items', ['code' => 'DRG-001', 'category' => 'drug']);
    }

    public function test_consignor_filter_includes_cross_supplier_delivered_items(): void
    {
        $custodian = User::where('email', 'aliana.dula@rmph.local')->first()
            ?? User::factory()->create()->assignRole('custodian');

        $terumo = Consignor::where('code', 'TERUMO')->first();
        $berovan = Consignor::where('code', 'BEROVAN')->first();

        $item = Item::where('code', 'SUP-001')->first();
        $this->assertEquals($terumo->id, $item->consignor_id);

        $delivery = Delivery::create([
            'consignor_id' => $berovan->id,
            'dr_number' => 'DR-TEST-CROSS',
            'delivery_date' => now()->toDateString(),
            'received_by_id' => $custodian->id,
        ]);

        Batch::create([
            'delivery_id' => $delivery->id,
            'item_id' => $item->id,
            'lot_number' => 'LOT-CROSS-01',
            'expiration_date' => now()->addYear()->toDateString(),
            'quantity_received' => 1500,
            'quantity_remaining' => 1500,
            'unit_cost' => 3.50,
            'is_active' => true,
        ]);

        $this->actingAs($custodian);

        Livewire::test(ListItems::class)
            ->filterTable('consignor_id', $berovan->id)
            ->assertCanSeeTableRecords([$item]);
    }
}
