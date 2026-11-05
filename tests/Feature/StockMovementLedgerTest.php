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
use Tests\TestCase;

class StockMovementLedgerTest extends TestCase
{
    use RefreshDatabase;

    public function test_perpetual_stockcard_ledger_running_balances_are_mathematically_exact(): void
    {
        $user = User::create([
            'name' => 'Custodian',
            'email' => 'cust@test.com',
            'password' => bcrypt('password'),
            'role' => 'medical_supply_staff',
            'department' => 'medical_supplies',
            'is_active' => true,
        ]);

        $supplier = Supplier::create([
            'name' => 'MediSupply Hub',
            'code' => 'SUP-HUB',
            'consignment_type' => 'medical_supplies',
            'is_active' => true,
        ]);

        $item = Item::create([
            'item_code' => 'GLV-001',
            'name' => 'Latex Gloves Medium',
            'department' => 'medical_supplies',
            'uom' => 'box',
            'supplier_id' => $supplier->id,
            'unit_cost' => 50.00,
            'minimum_stock_level' => 10,
            'is_active' => true,
        ]);

        $service = new InventoryService;

        // 1. Inward Delivery: 100 units
        $dr1 = Delivery::create([
            'delivery_number' => 'DR-SEQ-01',
            'supplier_id' => $supplier->id,
            'department' => 'medical_supplies',
            'delivery_date' => Carbon::today(),
            'received_by_id' => $user->id,
            'status' => 'received',
        ]);
        DeliveryItem::create([
            'delivery_id' => $dr1->id,
            'item_id' => $item->id,
            'lot_number' => 'LOT-01',
            'quantity_received' => 100,
            'quantity_remaining' => 100,
            'unit_cost' => 50.00,
            'total_cost' => 5000.00,
            'status' => 'available',
        ]);
        $service->processInwardDelivery($dr1, $user);

        // 2. Outward Request: 30 units
        $ris1 = StockRequest::create([
            'request_number' => 'RIS-SEQ-01',
            'department' => 'medical_supplies',
            'requesting_unit' => 'Emergency Room',
            'requester_name' => 'Nurse Dan',
            'request_date' => Carbon::today(),
            'status' => 'approved',
        ]);
        StockRequestItem::create([
            'stock_request_id' => $ris1->id,
            'item_id' => $item->id,
            'quantity_requested' => 30,
            'quantity_issued' => 0,
            'unit_cost' => 50.00,
            'total_cost' => 0.00,
        ]);
        $service->issueRequest($ris1, $user);

        // 3. Inward Delivery: 50 units
        $dr2 = Delivery::create([
            'delivery_number' => 'DR-SEQ-02',
            'supplier_id' => $supplier->id,
            'department' => 'medical_supplies',
            'delivery_date' => Carbon::today(),
            'received_by_id' => $user->id,
            'status' => 'received',
        ]);
        DeliveryItem::create([
            'delivery_id' => $dr2->id,
            'item_id' => $item->id,
            'lot_number' => 'LOT-02',
            'quantity_received' => 50,
            'quantity_remaining' => 50,
            'unit_cost' => 50.00,
            'total_cost' => 2500.00,
            'status' => 'available',
        ]);
        $service->processInwardDelivery($dr2, $user);

        // 4. Outward Request: 40 units
        $ris2 = StockRequest::create([
            'request_number' => 'RIS-SEQ-02',
            'department' => 'medical_supplies',
            'requesting_unit' => 'Operating Room',
            'requester_name' => 'Nurse Dan',
            'request_date' => Carbon::today(),
            'status' => 'approved',
        ]);
        StockRequestItem::create([
            'stock_request_id' => $ris2->id,
            'item_id' => $item->id,
            'quantity_requested' => 40,
            'quantity_issued' => 0,
            'unit_cost' => 50.00,
            'total_cost' => 0.00,
        ]);
        $service->issueRequest($ris2, $user);

        // Fetch movements in sequence
        $movements = StockMovement::where('item_id', $item->id)
            ->orderBy('id', 'asc')
            ->get();

        $this->assertCount(4, $movements);

        // Check running balances: 100 -> 70 -> 120 -> 80
        $this->assertEquals(100, $movements[0]->balance_after);
        $this->assertEquals(70, $movements[1]->balance_after);
        $this->assertEquals(120, $movements[2]->balance_after);
        $this->assertEquals(80, $movements[3]->balance_after);

        $this->assertEquals(80, $item->current_stock);
        $this->assertEquals(4000.00, $item->total_valuation);
    }
}
