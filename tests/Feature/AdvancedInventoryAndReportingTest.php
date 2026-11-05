<?php

namespace Tests\Feature;

use App\Filament\Pages\InventoryReports;
use App\Filament\Pages\StatementOfConsumedGoods;
use App\Models\Delivery;
use App\Models\DeliveryItem;
use App\Models\Item;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\User;
use App\Services\InventoryService;
use Carbon\Carbon;
use Database\Seeders\MedicalSuppliesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Tests\TestCase;

class AdvancedInventoryAndReportingTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private Supplier $supplier;

    private Item $item;

    private DeliveryItem $batch;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'department' => 'all',
            'is_active' => true,
        ]);

        $this->supplier = Supplier::create([
            'name' => 'Metro Drug Corp',
            'code' => 'SUP-METRO',
            'consignment_type' => 'pharmacy',
            'is_active' => true,
        ]);

        $this->item = Item::create([
            'item_code' => 'PHAR-PAR-500',
            'name' => 'Paracetamol 500mg Tab',
            'generic_name' => 'Paracetamol',
            'department' => 'pharmacy',
            'category' => 'Analgesics',
            'uom' => 'tablet',
            'supplier_id' => $this->supplier->id,
            'unit_cost' => 2.50,
            'minimum_stock_level' => 50,
            'is_active' => true,
        ]);

        $delivery = Delivery::create([
            'delivery_number' => 'DR-2026-001',
            'supplier_id' => $this->supplier->id,
            'department' => 'pharmacy',
            'delivery_date' => now()->toDateString(),
            'received_by_id' => $this->admin->id,
            'total_cost' => 250.00,
            'status' => 'received',
        ]);

        $this->batch = DeliveryItem::create([
            'delivery_id' => $delivery->id,
            'item_id' => $this->item->id,
            'lot_number' => 'LOT-PAR-01',
            'expiry_date' => Carbon::now()->addYear()->toDateString(),
            'quantity_received' => 100,
            'quantity_remaining' => 100,
            'unit_cost' => 2.50,
            'total_cost' => 250.00,
            'status' => 'available',
        ]);

        StockMovement::create([
            'item_id' => $this->item->id,
            'delivery_item_id' => $this->batch->id,
            'department' => 'pharmacy',
            'movement_type' => 'DELIVERY_IN',
            'reference_number' => 'DR-2026-001',
            'reference_type' => Delivery::class,
            'reference_id' => $delivery->id,
            'source_destination' => $this->supplier->name,
            'lot_number' => 'LOT-PAR-01',
            'expiry_date' => $this->batch->expiry_date,
            'quantity_in' => 100,
            'quantity_out' => 0,
            'balance_after' => 100,
            'unit_cost' => 2.50,
            'total_cost' => 250.00,
            'user_id' => $this->admin->id,
        ]);
    }

    public function test_stock_adjustment_deducts_batch_and_creates_movement(): void
    {
        $service = app(InventoryService::class);

        $movement = $service->adjustStock(
            item: $this->item,
            batch: $this->batch,
            quantity: 5,
            type: 'ADJUSTMENT',
            isDeduction: true,
            reason: 'Broken blister pack during inventory audit',
            actor: $this->admin
        );

        $this->batch->refresh();
        $this->assertEquals(95, $this->batch->quantity_remaining);
        $this->assertEquals('available', $this->batch->status);
        $this->assertEquals(95, $this->item->current_stock);

        $this->assertDatabaseHas('stock_movements', [
            'id' => $movement->id,
            'movement_type' => 'ADJUSTMENT',
            'quantity_in' => 0,
            'quantity_out' => 5,
            'balance_after' => 95,
            'remarks' => 'Broken blister pack during inventory audit',
        ]);
    }

    public function test_stock_adjustment_throws_when_deducting_more_than_available(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $service = app(InventoryService::class);

        $service->adjustStock(
            item: $this->item,
            batch: $this->batch,
            quantity: 150,
            type: 'ADJUSTMENT',
            isDeduction: true,
            reason: 'Excessive deduction',
            actor: $this->admin
        );
    }

    public function test_stock_adjustment_handles_surplus_addition(): void
    {
        $service = app(InventoryService::class);

        $movement = $service->adjustStock(
            item: $this->item,
            batch: $this->batch,
            quantity: 10,
            type: 'ADJUSTMENT',
            isDeduction: false,
            reason: 'Found misplaced stock in reserve shelf',
            actor: $this->admin
        );

        $this->batch->refresh();
        $this->assertEquals(110, $this->batch->quantity_remaining);
        $this->assertEquals(110, $this->item->current_stock);
        $this->assertEquals(10, $movement->quantity_in);
        $this->assertEquals(0, $movement->quantity_out);
    }

    public function test_inventory_report_exports_csv(): void
    {
        $this->actingAs($this->admin);

        $page = new InventoryReports;
        $page->preset = 'all_time';
        $page->applyPreset();

        $response = $page->exportCsv();

        $this->assertInstanceOf(StreamedResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue(str_contains($response->headers->get('content-disposition') ?? '', '.csv'));
    }

    public function test_official_inventory_report_printable_view_renders(): void
    {
        $response = $this->actingAs($this->admin)->get(route('inventory-reports.print', [
            'date_from' => now()->startOfMonth()->toDateString(),
            'date_to' => now()->endOfMonth()->toDateString(),
            'department' => 'pharmacy',
        ]));

        $response->assertStatus(200);
        $response->assertSee('ROXAS MEMORIAL PROVINCIAL HOSPITAL');
        $response->assertSee('Consignment Inventory Audit');
        $response->assertSee('Consumption Summary');
        $response->assertSee('PHAR-PAR-500');
        $response->assertSee('Commission on Audit (COA)');
    }

    public function test_stock_movement_ledger_renders_for_authorized_user(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/stock-movements');

        $response->assertStatus(200);
        $response->assertSee('Audit Movement Ledger');
        $response->assertSee('PHAR-PAR-500');
        $response->assertSee('DR-2026-001');
    }

    public function test_stcg_page_renders_and_exports_csv(): void
    {
        $this->actingAs($this->admin);

        $page = new StatementOfConsumedGoods;
        $page->supplierId = $this->supplier->id;
        $page->preset = 'this_month';
        $page->applyPreset();
        $page->generateStcgNumber();

        $response = $page->exportCsv();

        $this->assertInstanceOf(StreamedResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue(str_contains($response->headers->get('content-disposition') ?? '', '.csv'));
    }

    public function test_official_stcg_printable_view_renders(): void
    {
        $response = $this->actingAs($this->admin)->get(route('stcg.print', [
            'supplier' => $this->supplier->id,
            'date_from' => now()->startOfMonth()->toDateString(),
            'date_to' => now()->endOfMonth()->toDateString(),
            'stcg_number' => 'StCG-202611-SUP-METRO',
        ]));

        $response->assertStatus(200);
        $response->assertSee('ROXAS MEMORIAL PROVINCIAL HOSPITAL');
        $response->assertSee('STATEMENT OF CONSUMED GOODS');
        $response->assertSee($this->supplier->name);
        $response->assertSee('StCG-202611-SUP-METRO');
        $response->assertSee('Chief of Hospital');
    }

    public function test_medical_supplies_seeder_imports_consignors_and_items(): void
    {
        $this->seed(MedicalSuppliesSeeder::class);

        $this->assertDatabaseHas('suppliers', [
            'name' => 'MHJ MEDICAL AND LABORATORY SUPPLY',
        ]);

        $this->assertDatabaseHas('items', [
            'item_code' => 'HECSM-RMH-CU01',
            'department' => 'medical_supplies',
        ]);

        $this->assertDatabaseHas('deliveries', [
            'delivery_number' => 'CR-01-001-2025',
        ]);
    }
}
