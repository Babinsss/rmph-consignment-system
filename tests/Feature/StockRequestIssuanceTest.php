<?php

namespace Tests\Feature;

use App\Models\Delivery;
use App\Models\DeliveryItem;
use App\Models\Item;
use App\Models\StockMovement;
use App\Models\StockRequest;
use App\Models\StockRequestItem;
use App\Models\Supplier;
use App\Models\User;
use App\Services\InventoryService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

class StockRequestIssuanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_fefo_stock_issuance_drains_earliest_expiring_batches_first(): void
    {
        $user = User::create([
            'name' => 'Pharmacist',
            'email' => 'rph@test.com',
            'password' => bcrypt('password'),
            'role' => 'pharmacy_staff',
            'department' => 'pharmacy',
            'is_active' => true,
        ]);

        $supplier = Supplier::create([
            'name' => 'Supplier A',
            'code' => 'SUP-A',
            'consignment_type' => 'pharmacy',
            'is_active' => true,
        ]);

        $item = Item::create([
            'item_code' => 'PAR-IV-500',
            'name' => 'Paracetamol IV 500mg',
            'department' => 'pharmacy',
            'uom' => 'vial',
            'supplier_id' => $supplier->id,
            'unit_cost' => 20.00,
            'minimum_stock_level' => 10,
            'is_active' => true,
        ]);

        $delivery = Delivery::create([
            'delivery_number' => 'DR-BATCHES-01',
            'supplier_id' => $supplier->id,
            'department' => 'pharmacy',
            'delivery_date' => Carbon::today(),
            'received_by_id' => $user->id,
            'status' => 'received',
        ]);

        // Batch 1: Expiring in 2 months (Earliest - should be consumed first)
        $batch1 = DeliveryItem::create([
            'delivery_id' => $delivery->id,
            'item_id' => $item->id,
            'lot_number' => 'LOT-EARLY',
            'expiry_date' => Carbon::today()->addMonths(2),
            'quantity_received' => 30,
            'quantity_remaining' => 30,
            'unit_cost' => 20.00,
            'total_cost' => 600.00,
            'status' => 'available',
        ]);

        // Batch 2: Expiring in 8 months (Later)
        $batch2 = DeliveryItem::create([
            'delivery_id' => $delivery->id,
            'item_id' => $item->id,
            'lot_number' => 'LOT-LATER',
            'expiry_date' => Carbon::today()->addMonths(8),
            'quantity_received' => 50,
            'quantity_remaining' => 50,
            'unit_cost' => 20.00,
            'total_cost' => 1000.00,
            'status' => 'available',
        ]);

        $this->assertEquals(80, $item->current_stock);

        // Request 45 units: should consume ALL 30 from batch 1, and 15 from batch 2
        $request = StockRequest::create([
            'request_number' => 'RIS-ICU-001',
            'department' => 'pharmacy',
            'requesting_unit' => 'ICU',
            'requester_name' => 'Nurse Joy',
            'request_date' => Carbon::today(),
            'status' => 'approved',
        ]);

        $reqItem = StockRequestItem::create([
            'stock_request_id' => $request->id,
            'item_id' => $item->id,
            'quantity_requested' => 45,
            'quantity_issued' => 0,
            'unit_cost' => 20.00,
            'total_cost' => 0.00,
        ]);

        $service = new InventoryService;
        $service->issueRequest($request, $user);

        // Assert Batch 1 is depleted
        $batch1->refresh();
        $this->assertEquals(0, $batch1->quantity_remaining);
        $this->assertEquals('depleted', $batch1->status);

        // Assert Batch 2 has 35 remaining (50 - 15)
        $batch2->refresh();
        $this->assertEquals(35, $batch2->quantity_remaining);
        $this->assertEquals('available', $batch2->status);

        // Assert overall item current stock is 35
        $item->refresh();
        $this->assertEquals(35, $item->current_stock);

        // Assert request status is issued
        $request->refresh();
        $this->assertEquals('issued', $request->status);
        $this->assertEquals(45, $reqItem->refresh()->quantity_issued);

        // Assert 2 REQUEST_OUT movements recorded
        $outMovements = StockMovement::where('item_id', $item->id)
            ->where('movement_type', 'REQUEST_OUT')
            ->orderBy('id', 'asc')
            ->get();

        $this->assertCount(2, $outMovements);
        $this->assertEquals(30, $outMovements[0]->quantity_out);
        $this->assertEquals(15, $outMovements[1]->quantity_out);
    }

    public function test_insufficient_stock_throws_exception_and_leaves_inventory_intact(): void
    {
        $user = User::create([
            'name' => 'Pharmacist',
            'email' => 'rph2@test.com',
            'password' => bcrypt('password'),
            'role' => 'pharmacy_staff',
            'department' => 'pharmacy',
            'is_active' => true,
        ]);

        $supplier = Supplier::create([
            'name' => 'Supplier B',
            'code' => 'SUP-B',
            'consignment_type' => 'pharmacy',
            'is_active' => true,
        ]);

        $item = Item::create([
            'item_code' => 'MED-TEST-002',
            'name' => 'Syringe 10ml',
            'department' => 'medical_supplies',
            'uom' => 'piece',
            'supplier_id' => $supplier->id,
            'unit_cost' => 15.00,
            'minimum_stock_level' => 10,
            'is_active' => true,
        ]);

        $delivery = Delivery::create([
            'delivery_number' => 'DR-SHORT-01',
            'supplier_id' => $supplier->id,
            'department' => 'medical_supplies',
            'delivery_date' => Carbon::today(),
            'received_by_id' => $user->id,
            'status' => 'received',
        ]);

        $batch = DeliveryItem::create([
            'delivery_id' => $delivery->id,
            'item_id' => $item->id,
            'lot_number' => 'LOT-SHORT',
            'expiry_date' => Carbon::today()->addYear(),
            'quantity_received' => 20,
            'quantity_remaining' => 20,
            'unit_cost' => 15.00,
            'total_cost' => 300.00,
            'status' => 'available',
        ]);

        $request = StockRequest::create([
            'request_number' => 'RIS-EXCEED-01',
            'department' => 'medical_supplies',
            'requesting_unit' => 'Ward 4',
            'requester_name' => 'Nurse Ann',
            'request_date' => Carbon::today(),
            'status' => 'pending',
        ]);

        StockRequestItem::create([
            'stock_request_id' => $request->id,
            'item_id' => $item->id,
            'quantity_requested' => 50, // Exceeds 20 available!
            'quantity_issued' => 0,
            'unit_cost' => 15.00,
            'total_cost' => 0.00,
        ]);

        $this->expectException(InvalidArgumentException::class);

        $service = new InventoryService;
        $service->issueRequest($request, $user);

        // Batch remains untouched
        $batch->refresh();
        $this->assertEquals(20, $batch->quantity_remaining);
    }
}
