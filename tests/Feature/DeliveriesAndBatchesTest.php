<?php

namespace Tests\Feature;

use App\Enums\ItemCategory;
use App\Filament\Resources\Deliveries\Pages\CreateDelivery;
use App\Models\Batch;
use App\Models\Consignor;
use App\Models\Delivery;
use App\Models\Item;
use App\Models\StockMovement;
use App\Models\User;
use App\Services\DeliveryService;
use Carbon\Carbon;
use Database\Seeders\RoleAndPermissionSeeder;
use Database\Seeders\UnitSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DeliveriesAndBatchesTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $custodian;

    protected User $viewer;

    protected User $csrStaff;

    protected Consignor $consignor;

    protected Item $item;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(UnitSeeder::class);
        $this->seed(RoleAndPermissionSeeder::class);
        $this->seed(UserSeeder::class);

        $this->admin = User::where('email', 'admin@rmph.local')->first();
        $this->custodian = User::where('email', 'aliana.dula@rmph.local')->first();
        $this->viewer = User::where('email', 'chief@rmph.local')->first();
        $this->csrStaff = User::where('email', 'csr@rmph.local')->first();

        $this->consignor = Consignor::create([
            'name' => 'Zuellig Pharma Corp.',
            'code' => 'ZUELLIG',
            'is_active' => true,
        ]);

        $this->item = Item::create([
            'code' => 'SUP-001',
            'description' => 'Sterile Disposable Syringe 3ml',
            'category' => ItemCategory::Supply,
            'unit_of_measure' => 'box of 100',
            'consignor_id' => $this->consignor->id,
            'minimum_level' => 10,
            'is_active' => true,
        ]);
    }

    public function test_custodian_can_view_deliveries_and_batches_screens(): void
    {
        $this->actingAs($this->custodian);

        $this->get('/admin/deliveries')->assertStatus(200);
        $this->get('/admin/batches')->assertStatus(200);
    }

    public function test_chief_of_hospital_viewer_can_view_deliveries_and_batches_screens(): void
    {
        $this->actingAs($this->viewer);

        $this->get('/admin/deliveries')->assertStatus(200);
        $this->get('/admin/batches')->assertStatus(200);
    }

    public function test_ward_staff_cannot_access_deliveries_or_batches(): void
    {
        $this->actingAs($this->csrStaff);

        $this->get('/admin/deliveries')->assertStatus(403);
        $this->get('/admin/batches')->assertStatus(403);
    }

    public function test_delivery_service_records_delivery_creates_batches_and_initial_stock_movements(): void
    {
        $service = app(DeliveryService::class);
        $today = Carbon::today('Asia/Manila');

        $delivery = $service->recordDelivery([
            'consignor_id' => $this->consignor->id,
            'dr_number' => 'DR-TEST-001',
            'delivery_date' => $today,
            'received_by_id' => $this->custodian->id,
            'notes' => 'Test delivery batch',
            'batches' => [
                [
                    'item_id' => $this->item->id,
                    'lot_number' => 'LOT-ABC-123',
                    'expiration_date' => $today->copy()->addYear(),
                    'quantity_received' => 500,
                    'unit_cost' => 12.50,
                ],
            ],
        ]);

        $this->assertDatabaseHas('deliveries', [
            'id' => $delivery->id,
            'dr_number' => 'DR-TEST-001',
            'consignor_id' => $this->consignor->id,
        ]);

        $this->assertDatabaseHas('batches', [
            'delivery_id' => $delivery->id,
            'item_id' => $this->item->id,
            'lot_number' => 'LOT-ABC-123',
            'quantity_received' => 500,
            'quantity_remaining' => 500,
            'unit_cost' => 12.50,
        ]);

        $this->assertDatabaseHas('stock_movements', [
            'type' => StockMovement::TYPE_RECEIVE,
            'quantity' => 500,
            'balance_after' => 500,
            'reference_number' => 'DR-TEST-001',
        ]);

        $this->assertEquals(500, $delivery->total_units);
        $this->assertEquals(6250.00, $delivery->total_amount);
    }

    public function test_item_stock_aggregates_from_all_active_batches(): void
    {
        $service = app(DeliveryService::class);
        $today = Carbon::today('Asia/Manila');

        $service->recordDelivery([
            'consignor_id' => $this->consignor->id,
            'dr_number' => 'DR-1',
            'delivery_date' => $today,
            'received_by_id' => $this->custodian->id,
            'batches' => [
                [
                    'item_id' => $this->item->id,
                    'lot_number' => 'LOT-1',
                    'expiration_date' => $today->copy()->addMonths(6),
                    'quantity_received' => 100,
                    'unit_cost' => 10.00,
                ],
            ],
        ]);

        $service->recordDelivery([
            'consignor_id' => $this->consignor->id,
            'dr_number' => 'DR-2',
            'delivery_date' => $today,
            'received_by_id' => $this->custodian->id,
            'batches' => [
                [
                    'item_id' => $this->item->id,
                    'lot_number' => 'LOT-2',
                    'expiration_date' => $today->copy()->addMonths(12),
                    'quantity_received' => 200,
                    'unit_cost' => 10.00,
                ],
            ],
        ]);

        $this->assertEquals(300, $this->item->fresh()->current_stock);
        $this->assertFalse($this->item->fresh()->is_low_stock);
    }

    public function test_batch_expiration_helpers_and_scopes(): void
    {
        $today = Carbon::today('Asia/Manila');

        $delivery = Delivery::create([
            'consignor_id' => $this->consignor->id,
            'dr_number' => 'DR-EXP-TEST',
            'delivery_date' => $today,
            'received_by_id' => $this->custodian->id,
        ]);

        $nearExpiryBatch = Batch::create([
            'delivery_id' => $delivery->id,
            'item_id' => $this->item->id,
            'lot_number' => 'LOT-EXP-SOON',
            'expiration_date' => $today->copy()->addDays(30),
            'quantity_received' => 50,
            'quantity_remaining' => 50,
            'unit_cost' => 15.00,
            'is_active' => true,
        ]);

        $expiredBatch = Batch::create([
            'delivery_id' => $delivery->id,
            'item_id' => $this->item->id,
            'lot_number' => 'LOT-EXPIRED',
            'expiration_date' => $today->copy()->subDays(5),
            'quantity_received' => 20,
            'quantity_remaining' => 20,
            'unit_cost' => 15.00,
            'is_active' => true,
        ]);

        $this->assertTrue($nearExpiryBatch->is_expiring_soon);
        $this->assertFalse($nearExpiryBatch->is_expired);

        $this->assertTrue($expiredBatch->is_expired);
        $this->assertFalse($expiredBatch->is_expiring_soon);

        $this->assertEquals(1, Batch::expiringSoon(90)->where('id', $nearExpiryBatch->id)->count());
        $this->assertEquals(1, Batch::expired()->where('id', $expiredBatch->id)->count());
    }

    public function test_unit_costs_are_strictly_hidden_from_ward_staff(): void
    {
        $this->assertTrue($this->admin->canViewUnitCosts());
        $this->assertTrue($this->custodian->canViewUnitCosts());
        $this->assertTrue($this->viewer->canViewUnitCosts());
        $this->assertFalse($this->csrStaff->canViewUnitCosts());
    }

    public function test_delivery_create_form_renders_cleanly_without_type_error(): void
    {
        $this->actingAs($this->custodian);

        Livewire::test(CreateDelivery::class)
            ->assertSuccessful()
            ->assertSee('Live Delivery Totals');
    }
}
