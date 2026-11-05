<?php

namespace Tests\Feature;

use App\Models\Delivery;
use App\Models\DeliveryItem;
use App\Models\Item;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\User;
use App\Services\InventoryService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeliveryProcessingTest extends TestCase
{
    use RefreshDatabase;

    public function test_inward_delivery_creates_batches_and_stock_movements(): void
    {
        $user = User::create([
            'name' => 'Custodian',
            'email' => 'custodian@test.com',
            'password' => bcrypt('password'),
            'role' => 'pharmacy_staff',
            'department' => 'pharmacy',
            'is_active' => true,
        ]);

        $supplier = Supplier::create([
            'name' => 'Test Supplier Inc',
            'code' => 'SUP-TEST',
            'consignment_type' => 'pharmacy',
            'is_active' => true,
        ]);

        $item = Item::create([
            'item_code' => 'PHAR-001',
            'name' => 'Test Amoxicillin 500mg',
            'department' => 'pharmacy',
            'uom' => 'capsule',
            'supplier_id' => $supplier->id,
            'unit_cost' => 10.00,
            'minimum_stock_level' => 20,
            'is_active' => true,
        ]);

        $this->assertEquals(0, $item->current_stock);

        $delivery = Delivery::create([
            'delivery_number' => 'DR-TEST-001',
            'supplier_id' => $supplier->id,
            'department' => 'pharmacy',
            'delivery_date' => Carbon::today(),
            'received_by_id' => $user->id,
            'status' => 'received',
        ]);

        $batch = DeliveryItem::create([
            'delivery_id' => $delivery->id,
            'item_id' => $item->id,
            'lot_number' => 'LOT-AMOX-100',
            'expiry_date' => Carbon::today()->addYear(),
            'quantity_received' => 150,
            'quantity_remaining' => 150,
            'unit_cost' => 10.00,
            'total_cost' => 1500.00,
            'status' => 'available',
        ]);

        $service = new InventoryService;
        $service->processInwardDelivery($delivery, $user);

        // Assert item stock increased
        $item->refresh();
        $this->assertEquals(150, $item->current_stock);
        $this->assertEquals(1500.00, $item->total_valuation);
        $this->assertEquals('in_stock', $item->stock_status);

        // Assert StockMovement recorded
        $movement = StockMovement::where('item_id', $item->id)->first();
        $this->assertNotNull($movement);
        $this->assertEquals('DELIVERY_IN', $movement->movement_type);
        $this->assertEquals('DR-TEST-001', $movement->reference_number);
        $this->assertEquals(150, $movement->quantity_in);
        $this->assertEquals(0, $movement->quantity_out);
        $this->assertEquals(150, $movement->balance_after);
        $this->assertEquals($batch->id, $movement->delivery_item_id);
    }
}
