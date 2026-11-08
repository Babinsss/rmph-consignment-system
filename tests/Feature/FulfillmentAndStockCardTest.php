<?php

namespace Tests\Feature;

use App\Enums\ItemCategory;
use App\Enums\RequestStatus;
use App\Models\Batch;
use App\Models\Consignor;
use App\Models\Delivery;
use App\Models\Item;
use App\Models\Request;
use App\Models\StockMovement;
use App\Models\Unit;
use App\Models\User;
use App\Services\FulfillmentService;
use Carbon\Carbon;
use Database\Seeders\RoleAndPermissionSeeder;
use Database\Seeders\UnitSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class FulfillmentAndStockCardTest extends TestCase
{
    use RefreshDatabase;

    protected User $custodian;

    protected User $viewer;

    protected User $csrStaff;

    protected Unit $csrUnit;

    protected Item $item;

    protected Batch $earlyBatch;

    protected Batch $lateBatch;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(UnitSeeder::class);
        $this->seed(RoleAndPermissionSeeder::class);
        $this->seed(UserSeeder::class);

        $this->custodian = User::where('email', 'aliana.dula@rmph.local')->first();
        $this->viewer = User::where('email', 'chief@rmph.local')->first();
        $this->csrStaff = User::where('email', 'csr@rmph.local')->first();
        $this->csrUnit = Unit::where('code', 'CSR')->first();

        $consignor = Consignor::create([
            'name' => 'Zuellig Pharma Corp.',
            'code' => 'ZUELLIG',
            'is_active' => true,
        ]);

        $this->item = Item::create([
            'code' => 'SUP-001',
            'description' => 'Sterile Syringe 3ml',
            'category' => ItemCategory::Supply,
            'unit_of_measure' => 'box of 100',
            'consignor_id' => $consignor->id,
            'minimum_level' => 10,
            'is_active' => true,
        ]);

        $today = Carbon::today('Asia/Manila');

        $delivery = Delivery::create([
            'consignor_id' => $consignor->id,
            'dr_number' => 'DR-TEST-FEFO',
            'delivery_date' => $today,
            'received_by_id' => $this->custodian->id,
        ]);

        // Early expiring batch (Expires in 2 months)
        $this->earlyBatch = Batch::create([
            'delivery_id' => $delivery->id,
            'item_id' => $this->item->id,
            'lot_number' => 'LOT-EARLY',
            'expiration_date' => $today->copy()->addMonths(2),
            'quantity_received' => 100,
            'quantity_remaining' => 100,
            'unit_cost' => 5.00,
            'is_active' => true,
        ]);

        // Later expiring batch (Expires in 12 months)
        $this->lateBatch = Batch::create([
            'delivery_id' => $delivery->id,
            'item_id' => $this->item->id,
            'lot_number' => 'LOT-LATE',
            'expiration_date' => $today->copy()->addMonths(12),
            'quantity_received' => 500,
            'quantity_remaining' => 500,
            'unit_cost' => 5.00,
            'is_active' => true,
        ]);
    }

    public function test_fefo_recommendations_pick_earliest_expiring_batch_first(): void
    {
        $request = Request::create([
            'request_number' => 'REQ-TEST-FEFO-01',
            'unit_id' => $this->csrUnit->id,
            'requested_by_id' => $this->csrStaff->id,
            'request_date' => now(),
            'is_urgent' => false,
            'status' => RequestStatus::Pending,
        ]);

        $line = $request->lines()->create([
            'item_id' => $this->item->id,
            'quantity_requested' => 150,
            'quantity_issued' => 0,
        ]);

        $service = app(FulfillmentService::class);
        $recs = $service->getFefoRecommendations($request);

        $lineRec = $recs[$line->id];
        $this->assertTrue($lineRec['has_sufficient_stock']);
        $this->assertEquals(150, $lineRec['total_suggested']);

        // First recommended batch should be LOT-EARLY with 100 units
        $this->assertEquals($this->earlyBatch->id, $lineRec['batches'][0]['batch_id']);
        $this->assertEquals(100, $lineRec['batches'][0]['suggested_issue_quantity']);

        // Second recommended batch should be LOT-LATE with remaining 50 units
        $this->assertEquals($this->lateBatch->id, $lineRec['batches'][1]['batch_id']);
        $this->assertEquals(50, $lineRec['batches'][1]['suggested_issue_quantity']);
    }

    public function test_fulfillment_deducts_stock_and_creates_immutable_issue_stock_movements(): void
    {
        $request = Request::create([
            'request_number' => 'REQ-TEST-FULFILL-01',
            'unit_id' => $this->csrUnit->id,
            'requested_by_id' => $this->csrStaff->id,
            'request_date' => now(),
            'is_urgent' => false,
            'status' => RequestStatus::Pending,
        ]);

        $line = $request->lines()->create([
            'item_id' => $this->item->id,
            'quantity_requested' => 100,
            'quantity_issued' => 0,
        ]);

        $service = app(FulfillmentService::class);
        $service->fulfillRequest($request, [
            [
                'line_id' => $line->id,
                'batch_id' => $this->earlyBatch->id,
                'quantity' => 100,
            ],
        ], $this->custodian);

        $this->assertEquals(0, $this->earlyBatch->fresh()->quantity_remaining);
        $this->assertEquals(RequestStatus::Fulfilled, $request->fresh()->status);
        $this->assertEquals(100, $line->fresh()->quantity_issued);

        $this->assertDatabaseHas('stock_movements', [
            'type' => StockMovement::TYPE_ISSUE,
            'batch_id' => $this->earlyBatch->id,
            'quantity' => -100,
            'balance_after' => 0,
            'reference_number' => 'REQ-TEST-FULFILL-01',
        ]);
    }

    public function test_partial_fulfillment_updates_status_to_partially_fulfilled(): void
    {
        $request = Request::create([
            'request_number' => 'REQ-TEST-PARTIAL',
            'unit_id' => $this->csrUnit->id,
            'requested_by_id' => $this->csrStaff->id,
            'request_date' => now(),
            'is_urgent' => false,
            'status' => RequestStatus::Pending,
        ]);

        $line = $request->lines()->create([
            'item_id' => $this->item->id,
            'quantity_requested' => 100,
            'quantity_issued' => 0,
        ]);

        $service = app(FulfillmentService::class);
        $service->fulfillRequest($request, [
            [
                'line_id' => $line->id,
                'batch_id' => $this->earlyBatch->id,
                'quantity' => 40, // Only 40 of 100 issued
            ],
        ], $this->custodian);

        $this->assertEquals(RequestStatus::PartiallyFulfilled, $request->fresh()->status);
        $this->assertEquals(40, $line->fresh()->quantity_issued);
        $this->assertEquals(60, $line->fresh()->pending_quantity);
    }

    public function test_physical_stock_adjustment_updates_balance_and_records_audit_trail(): void
    {
        $service = app(FulfillmentService::class);

        // Discovered 5 broken syringes discarded during inventory count
        $updatedBatch = $service->adjustStock(
            $this->earlyBatch,
            95,
            'Discarded 5 broken units discovered during physical inventory check.',
            $this->custodian
        );

        $this->assertEquals(95, $updatedBatch->quantity_remaining);

        $this->assertDatabaseHas('stock_movements', [
            'type' => StockMovement::TYPE_ADJUST,
            'batch_id' => $this->earlyBatch->id,
            'quantity' => -5,
            'balance_after' => 95,
        ]);
    }

    public function test_physical_stock_adjustment_requires_mandatory_reason(): void
    {
        $this->expectException(ValidationException::class);

        $service = app(FulfillmentService::class);
        $service->adjustStock(
            $this->earlyBatch,
            95,
            '', // Empty reason should fail
            $this->custodian
        );
    }

    public function test_stock_movement_screen_is_accessible_to_custodians_and_chief(): void
    {
        $this->actingAs($this->custodian);
        $this->get('/admin/stock-movements')->assertStatus(200);

        $this->actingAs($this->viewer);
        $this->get('/admin/stock-movements')->assertStatus(200);
    }
}
