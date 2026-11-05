<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\StockRequest;
use App\Models\StockRequestItem;
use App\Models\Supplier;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockcardPrintTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_for_stockcard(): void
    {
        $supplier = Supplier::create([
            'name' => 'Supplier C',
            'code' => 'SUP-C',
            'is_active' => true,
        ]);

        $item = Item::create([
            'item_code' => 'MED-PRINT-01',
            'name' => 'Print Test Item',
            'department' => 'medical_supplies',
            'uom' => 'piece',
            'supplier_id' => $supplier->id,
            'unit_cost' => 10.00,
            'is_active' => true,
        ]);

        $response = $this->get(route('stockcard.print', ['item' => $item->id]));
        $response->assertRedirect('/admin/login');
    }

    public function test_authenticated_user_can_view_printable_stockcard(): void
    {
        $user = User::create([
            'name' => 'Nurse Joy',
            'email' => 'joy@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'department' => 'all',
            'is_active' => true,
        ]);

        $supplier = Supplier::create([
            'name' => 'Supplier C',
            'code' => 'SUP-C',
            'is_active' => true,
        ]);

        $item = Item::create([
            'item_code' => 'MED-PRINT-02',
            'name' => 'Sterile Gauze 4x4',
            'department' => 'medical_supplies',
            'uom' => 'pack',
            'supplier_id' => $supplier->id,
            'unit_cost' => 25.00,
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->get(route('stockcard.print', ['item' => $item->id]));

        $response->assertOk();
        $response->assertSee('CONSIGNMENT STOCK CARD');
        $response->assertSee('ROXAS MEMORIAL PROVINCIAL HOSPITAL');
        $response->assertSee('MED-PRINT-02');
        $response->assertSee('Sterile Gauze 4x4');
    }

    public function test_authenticated_user_can_view_printable_requisition_slip(): void
    {
        $user = User::create([
            'name' => 'Nurse Joy',
            'email' => 'joy2@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'department' => 'all',
            'is_active' => true,
        ]);

        $supplier = Supplier::create([
            'name' => 'Supplier C',
            'code' => 'SUP-C',
            'is_active' => true,
        ]);

        $item = Item::create([
            'item_code' => 'PHAR-PRINT-01',
            'name' => 'Cefuroxime 750mg Vial',
            'department' => 'pharmacy',
            'uom' => 'vial',
            'supplier_id' => $supplier->id,
            'unit_cost' => 120.00,
            'is_active' => true,
        ]);

        $request = StockRequest::create([
            'request_number' => 'RIS-PRINT-01',
            'department' => 'pharmacy',
            'requesting_unit' => 'ICU - Critical Care',
            'requester_name' => 'Nurse Joy',
            'request_date' => Carbon::today(),
            'status' => 'approved',
        ]);

        StockRequestItem::create([
            'stock_request_id' => $request->id,
            'item_id' => $item->id,
            'quantity_requested' => 10,
            'quantity_issued' => 10,
            'unit_cost' => 120.00,
            'total_cost' => 1200.00,
        ]);

        $response = $this->actingAs($user)->get(route('requisition.print', ['stockRequest' => $request->id]));

        $response->assertOk();
        $response->assertSee('REQUISITION AND ISSUE SLIP (RIS)');
        $response->assertSee('RIS-PRINT-01');
        $response->assertSee('ICU - Critical Care');
        $response->assertSee('Cefuroxime 750mg Vial');
    }
}
