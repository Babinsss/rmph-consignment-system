<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\StockRequest;
use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DepartmentPortalTest extends TestCase
{
    use RefreshDatabase;

    private Supplier $supplier;

    private Item $item;

    protected function setUp(): void
    {
        parent::setUp();

        $this->supplier = Supplier::create([
            'name' => 'Berovan Marketing Inc.',
            'code' => 'SUP-BEROVAN',
            'consignment_type' => 'medical_supplies',
            'is_active' => true,
        ]);

        $this->item = Item::create([
            'item_code' => 'BER-GLV-01',
            'name' => 'Sterile Gloves 7.0',
            'department' => 'medical_supplies',
            'uom' => 'pair',
            'supplier_id' => $this->supplier->id,
            'unit_cost' => 42.00,
            'minimum_stock_level' => 50,
            'is_active' => true,
        ]);
    }

    public function test_can_render_department_portal_page(): void
    {
        $response = $this->get('/portal');

        $response->assertSuccessful();
        $response->assertSee('ROXAS MEMORIAL PROVINCIAL HOSPITAL');
        $response->assertSee('Consignment Requisition');
        $response->assertSee('CSR (Central Supply Room)');
        $response->assertSee('HDU (High Dependency Unit)');
        $response->assertSee('Gugma Dialysis Center');
        $response->assertSee('Pharmacy Main');
        $response->assertSee('Pharmacy OPD');
        $response->assertSee('Yakap Center');
        $response->assertSee('Sterile Gloves 7.0');
    }

    public function test_can_submit_requisition_from_portal_and_creates_pending_stock_request(): void
    {
        $payload = [
            'department_name' => 'CSR',
            'requested_by' => 'Mia Buenvenida, RN',
            'purpose' => 'Urgent replenishment for clinical ward stocks',
            'is_emergency' => '1',
            'patient_mrn' => 'CSR-BATCH-99',
            'cart_data' => json_encode([
                [
                    'id' => $this->item->id,
                    'name' => $this->item->name,
                    'qty' => 20,
                    'uom' => $this->item->uom,
                ],
            ]),
        ];

        $response = $this->post('/portal/submit', $payload);

        $response->assertRedirect('/portal');
        $response->assertSessionHas('success');
        $response->assertSessionHas('stock_request_id');

        $this->assertDatabaseHas('stock_requests', [
            'requesting_unit' => 'CSR',
            'requester_name' => 'Mia Buenvenida, RN',
            'status' => 'pending',
            'patient_mrn' => 'CSR-BATCH-99',
        ]);

        $stockRequest = StockRequest::where('requesting_unit', 'CSR')->first();
        $this->assertNotNull($stockRequest);
        $this->assertCount(1, $stockRequest->items);
        $this->assertEquals(20, $stockRequest->items->first()->quantity_requested);
        $this->assertEquals($this->item->id, $stockRequest->items->first()->item_id);
    }

    public function test_submitting_with_empty_cart_fails_validation(): void
    {
        $payload = [
            'department_name' => 'HDU',
            'requested_by' => 'Nurse Joy',
            'purpose' => 'Replenishment',
            'is_emergency' => '1',
            'cart_data' => json_encode([]),
        ];

        $response = $this->post('/portal/submit', $payload);

        $response->assertRedirect('/portal');
        $response->assertSessionHas('danger');
        $this->assertDatabaseCount('stock_requests', 0);
    }

    public function test_submitting_with_unauthorized_department_fails_validation(): void
    {
        $payload = [
            'department_name' => 'Unauthorized Ward',
            'requested_by' => 'Nurse Joy',
            'purpose' => 'Replenishment',
            'is_emergency' => '1',
            'cart_data' => json_encode([
                ['id' => $this->item->id, 'qty' => 5],
            ]),
        ];

        $response = $this->post('/portal/submit', $payload);

        $response->assertSessionHasErrors(['department_name']);
        $this->assertDatabaseCount('stock_requests', 0);
    }

    public function test_direct_ris_printable_route_accessible_from_portal_submission(): void
    {
        $stockRequest = StockRequest::create([
            'request_number' => 'RIS-PORTAL-TEST',
            'department' => 'medical_supplies',
            'requesting_unit' => 'GUGMA DIALYSIS CENTER',
            'requester_name' => 'Stephen Espenocilla',
            'request_date' => now(),
            'status' => 'pending',
            'purpose' => 'Dialysis session replenishment',
        ]);

        $response = $this->get("/requisition/{$stockRequest->id}");

        $response->assertSuccessful();
        $response->assertSee('REQUISITION AND ISSUE SLIP (RIS)');
        $response->assertSee('GUGMA DIALYSIS CENTER');
        $response->assertSee('Stephen Espenocilla');
        $response->assertSee('RIS-PORTAL-TEST');
    }
}
