<?php

namespace Tests\Feature;

use App\Models\Delivery;
use App\Models\DeliveryItem;
use App\Models\Item;
use App\Models\StockRequest;
use App\Models\StockRequestItem;
use App\Models\Supplier;
use App\Models\User;
use App\Services\InventoryService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RmphHospitalFormsAndRolesTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $medOfficer;

    private User $pharmOfficer;

    private Supplier $supplier;

    private Item $medItem;

    private Item $pharmItem;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Florencio O. Luching Jr., M.D., FPCS',
            'email' => 'coh@rmph.gov.ph',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'department' => 'all',
            'is_active' => true,
        ]);

        $this->medOfficer = User::create([
            'name' => 'Aliana Marie Dula',
            'email' => 'aliana.dula@rmph.gov.ph',
            'password' => bcrypt('password'),
            'role' => 'medical_supply_staff',
            'department' => 'medical_supplies',
            'is_active' => true,
        ]);

        $this->pharmOfficer = User::create([
            'name' => 'Kristine Mae Batan',
            'email' => 'kristine.batan@rmph.gov.ph',
            'password' => bcrypt('password'),
            'role' => 'pharmacy_staff',
            'department' => 'pharmacy',
            'is_active' => true,
        ]);

        $this->supplier = Supplier::create([
            'name' => 'Metro Drug Inc.',
            'code' => 'SUP-METRO',
            'consignment_type' => 'both',
            'is_active' => true,
        ]);

        $this->medItem = Item::create([
            'item_code' => 'MED-GLV-001',
            'name' => 'Surgical Gloves Size 7.5',
            'department' => 'medical_supplies',
            'uom' => 'pair',
            'supplier_id' => $this->supplier->id,
            'unit_cost' => 35.00,
            'minimum_stock_level' => 50,
            'is_active' => true,
        ]);

        $this->pharmItem = Item::create([
            'item_code' => 'PHA-PAR-500',
            'name' => 'Paracetamol IV 500mg',
            'department' => 'pharmacy',
            'uom' => 'vial',
            'supplier_id' => $this->supplier->id,
            'unit_cost' => 22.00,
            'minimum_stock_level' => 30,
            'is_active' => true,
        ]);
    }

    public function test_stock_request_allowed_departments_list_contains_only_the_authorized_six(): void
    {
        $allowed = StockRequest::ALLOWED_REQUESTING_UNITS;

        $this->assertCount(6, $allowed);
        $this->assertArrayHasKey('CSR', $allowed);
        $this->assertArrayHasKey('HDU', $allowed);
        $this->assertArrayHasKey('GUGMA DIALYSIS CENTER', $allowed);
        $this->assertArrayHasKey('PHARMACY MAIN', $allowed);
        $this->assertArrayHasKey('PHARMACY OPD', $allowed);
        $this->assertArrayHasKey('YAKAP', $allowed);
    }

    public function test_bincard_renders_official_hospital_f02_format(): void
    {
        $delivery = Delivery::create([
            'delivery_number' => 'DR-BIN-001',
            'supplier_id' => $this->supplier->id,
            'department' => 'medical_supplies',
            'delivery_date' => Carbon::today(),
            'received_by_id' => $this->medOfficer->id,
            'status' => 'received',
        ]);

        DeliveryItem::create([
            'delivery_id' => $delivery->id,
            'item_id' => $this->medItem->id,
            'lot_number' => 'LOT-BIN-77',
            'expiry_date' => Carbon::today()->addYear(),
            'quantity_received' => 100,
            'quantity_remaining' => 100,
            'unit_cost' => 35.00,
            'total_cost' => 3500.00,
            'status' => 'available',
        ]);

        (new InventoryService)->processInwardDelivery($delivery, $this->medOfficer);

        $response = $this->actingAs($this->admin)->get("/bincard/{$this->medItem->id}");

        $response->assertSuccessful();
        $response->assertSee('RMH-PPS-F02');
        $response->assertSee('BIN CARD');
        $response->assertSee('Surgical Gloves Size 7.5');
        $response->assertSee('100');
    }

    public function test_rapc_renders_correct_consignor_and_signatories_without_lady_luces(): void
    {
        // Medical Supplies RAPC
        $responseMed = $this->actingAs($this->admin)->get("/rapc/{$this->medItem->id}?department=medical_supplies");

        $responseMed->assertSuccessful();
        $responseMed->assertSee('REPORT ON ACTUAL PHYSICAL COUNT');
        $responseMed->assertSee('ALIANA MARIE DULA');
        $responseMed->assertSee('Consignment Officer - Medical Supplies');
        $responseMed->assertSee('FLORENCIO O. LUCHING JR., M.D., FPCS');
        $responseMed->assertDontSee('Lady Ortalez S. Luces');

        // Pharmacy RAPC
        $responsePharm = $this->actingAs($this->admin)->get("/rapc/{$this->pharmItem->id}?department=pharmacy");

        $responsePharm->assertSuccessful();
        $responsePharm->assertSee('KRISTINE MAE BATAN');
        $responsePharm->assertSee('Consignment Officer - Pharmacy Stocks');
        $responsePharm->assertSee('FLORENCIO O. LUCHING JR., M.D., FPCS');
        $responsePharm->assertDontSee('Lady Ortalez S. Luces');
    }

    public function test_purchase_request_appendix_47_renders_with_governor_and_chief_of_hospital(): void
    {
        $response = $this->actingAs($this->admin)->get('/purchase-request/critical');

        $response->assertSuccessful();
        $response->assertSee('Appendix 47');
        $response->assertSee('PURCHASE REQUEST');
        $response->assertSee('FLORENCIO O. LUCHING JR., M.D., FPCS');
        $response->assertSee('Chief of Hospital II');
        $response->assertSee('FREDENIL H. CASTRO');
        $response->assertSee('Provincial Governor');
        $response->assertSee('PROVINCIAL GOVERNMENT OF CAPIZ');
    }

    public function test_item_usage_report_ranks_authorized_departments(): void
    {
        $delivery = Delivery::create([
            'delivery_number' => 'DR-USAGE-001',
            'supplier_id' => $this->supplier->id,
            'department' => 'medical_supplies',
            'delivery_date' => Carbon::today(),
            'received_by_id' => $this->medOfficer->id,
            'status' => 'received',
        ]);

        DeliveryItem::create([
            'delivery_id' => $delivery->id,
            'item_id' => $this->medItem->id,
            'lot_number' => 'LOT-USAGE-01',
            'expiry_date' => Carbon::today()->addYear(),
            'quantity_received' => 200,
            'quantity_remaining' => 200,
            'unit_cost' => 35.00,
            'total_cost' => 7000.00,
            'status' => 'available',
        ]);

        (new InventoryService)->processInwardDelivery($delivery, $this->medOfficer);

        // Request 1 from CSR
        $reqCsr = StockRequest::create([
            'request_number' => 'RIS-CSR-001',
            'department' => 'medical_supplies',
            'requesting_unit' => 'CSR',
            'requester_name' => 'Mia Buenvenida, RN',
            'request_date' => Carbon::today(),
            'status' => 'approved',
        ]);
        StockRequestItem::create([
            'stock_request_id' => $reqCsr->id,
            'item_id' => $this->medItem->id,
            'quantity_requested' => 40,
        ]);
        (new InventoryService)->issueRequest($reqCsr, $this->medOfficer);

        // Request 2 from HDU
        $reqHdu = StockRequest::create([
            'request_number' => 'RIS-HDU-001',
            'department' => 'medical_supplies',
            'requesting_unit' => 'HDU',
            'requester_name' => 'HDU Nurse',
            'request_date' => Carbon::today(),
            'status' => 'approved',
        ]);
        StockRequestItem::create([
            'stock_request_id' => $reqHdu->id,
            'item_id' => $this->medItem->id,
            'quantity_requested' => 25,
        ]);
        (new InventoryService)->issueRequest($reqHdu, $this->medOfficer);

        $response = $this->actingAs($this->admin)->get("/item-usage/{$this->medItem->id}");

        $response->assertSuccessful();
        $response->assertSee('Departmental Consignment Item Usage Report');
        $response->assertSee('CSR');
        $response->assertSee('HDU');
        $response->assertSee('ALIANA MARIE DULA');
        $response->assertDontSee('Lady Ortalez S. Luces');
    }

    public function test_stockcard_and_stcg_render_exact_consignment_officers(): void
    {
        // Stockcard for Medical Supplies
        $respStockMed = $this->actingAs($this->admin)->get("/stockcard/{$this->medItem->id}");
        $respStockMed->assertSuccessful();
        $respStockMed->assertSee('ALIANA MARIE DULA');
        $respStockMed->assertSee('Consignment Officer - Medical Supplies');
        $respStockMed->assertDontSee('Lady Ortalez S. Luces');

        // Stockcard for Pharmacy
        $respStockPharm = $this->actingAs($this->admin)->get("/stockcard/{$this->pharmItem->id}");
        $respStockPharm->assertSuccessful();
        $respStockPharm->assertSee('KRISTINE MAE BATAN');
        $respStockPharm->assertSee('Consignment Officer - Pharmacy Stocks');
        $respStockPharm->assertDontSee('Lady Ortalez S. Luces');

        // StCG
        $respStcg = $this->actingAs($this->admin)->get("/stcg/{$this->supplier->id}");
        $respStcg->assertSuccessful();
        $respStcg->assertSee('FLORENCIO O. LUCHING JR., M.D., FPCS');
        $respStcg->assertSee('JAHZEILE SALISTRE');
        $respStcg->assertDontSee('Lady Ortalez S. Luces');
    }
}
