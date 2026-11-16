<?php

namespace Tests\Feature;

use App\Enums\ItemCategory;
use App\Enums\RequestStatus;
use App\Models\Batch;
use App\Models\Consignor;
use App\Models\Delivery;
use App\Models\Item;
use App\Models\Request;
use App\Models\Unit;
use App\Models\User;
use App\Services\FulfillmentService;
use Carbon\Carbon;
use Database\Seeders\RoleAndPermissionSeeder;
use Database\Seeders\UnitSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportsAndExportsTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $custodian;

    protected User $viewer;

    protected User $csrStaff;

    protected Unit $csrUnit;

    protected Item $item;

    protected Batch $batch;

    protected Delivery $delivery;

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
        $this->csrUnit = Unit::where('code', 'CSR')->first();

        $consignor = Consignor::create([
            'name' => 'Zuellig Pharma Corp.',
            'code' => 'ZUELLIG',
            'is_active' => true,
        ]);

        $this->item = Item::create([
            'code' => 'SUP-001',
            'description' => 'Sterile Syringe 3ml with needle',
            'category' => ItemCategory::Supply,
            'unit_of_measure' => 'box of 100',
            'consignor_id' => $consignor->id,
            'minimum_level' => 10,
            'is_active' => true,
        ]);

        $this->delivery = Delivery::create([
            'consignor_id' => $consignor->id,
            'dr_number' => 'DR-REP-001',
            'delivery_date' => Carbon::today('Asia/Manila'),
            'received_by_id' => $this->custodian->id,
        ]);

        $this->batch = Batch::create([
            'delivery_id' => $this->delivery->id,
            'item_id' => $this->item->id,
            'lot_number' => 'LOT-REP-01',
            'expiration_date' => Carbon::today('Asia/Manila')->addDays(45), // Within 90 days
            'quantity_received' => 200,
            'quantity_remaining' => 200,
            'unit_cost' => 4.50,
            'is_active' => true,
        ]);

        $req = Request::create([
            'request_number' => 'REQ-REP-001',
            'unit_id' => $this->csrUnit->id,
            'requested_by_id' => $this->csrStaff->id,
            'request_date' => now(),
            'is_urgent' => false,
            'status' => RequestStatus::Pending,
        ]);

        $req->lines()->create([
            'item_id' => $this->item->id,
            'quantity_requested' => 50,
            'quantity_issued' => 0,
        ]);
    }

    public function test_reports_page_is_accessible_to_custodians_and_chief(): void
    {
        $this->actingAs($this->custodian);
        $this->get('/admin/reports')->assertStatus(200);

        $this->actingAs($this->viewer);
        $this->get('/admin/reports')->assertStatus(200);
    }

    public function test_reports_page_is_forbidden_to_ward_staff(): void
    {
        $this->actingAs($this->csrStaff);
        $this->get('/admin/reports')->assertStatus(403);
    }

    public function test_stock_on_hand_pdf_downloads_successfully(): void
    {
        $this->actingAs($this->custodian);

        $response = $this->get('/reports/stock-on-hand/pdf');
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_stock_on_hand_csv_exports_valid_data(): void
    {
        $this->actingAs($this->custodian);

        $response = $this->get('/reports/stock-on-hand/csv');
        $response->assertStatus(200);
        $this->assertStringContainsString('text/csv', $response->headers->get('Content-Type'));

        $content = $response->streamedContent();
        $this->assertStringContainsString('Item Code', $content);
        $this->assertStringContainsString('Description', $content);
        $this->assertStringContainsString('SUP-001', $content);
        $this->assertStringContainsString('Sterile Syringe 3ml', $content);
    }

    public function test_near_expiry_pdf_downloads_successfully(): void
    {
        $this->actingAs($this->viewer);

        $response = $this->get('/reports/near-expiry/pdf');
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_near_expiry_csv_exports_flagged_batches(): void
    {
        $this->actingAs($this->custodian);

        $response = $this->get('/reports/near-expiry/csv');
        $response->assertStatus(200);

        $content = $response->streamedContent();
        $this->assertStringContainsString('LOT-REP-01', $content);
        $this->assertStringContainsString('SUP-001', $content);
    }

    public function test_deliveries_pdf_and_csv_export_successfully(): void
    {
        $this->actingAs($this->custodian);

        $this->get('/reports/deliveries/pdf')
            ->assertStatus(200)
            ->assertHeader('Content-Type', 'application/pdf');

        $csvResponse = $this->get('/reports/deliveries/csv');
        $csvResponse->assertStatus(200);
        $this->assertStringContainsString('DR-REP-001', $csvResponse->streamedContent());
    }

    public function test_unit_requests_pdf_and_csv_export_successfully(): void
    {
        $this->actingAs($this->viewer);

        $this->get('/reports/unit-requests/pdf')
            ->assertStatus(200)
            ->assertHeader('Content-Type', 'application/pdf');

        $csvResponse = $this->get('/reports/unit-requests/csv');
        $csvResponse->assertStatus(200);
        $this->assertStringContainsString('REQ-REP-001', $csvResponse->streamedContent());
    }

    public function test_unauthorized_ward_staff_cannot_download_reports(): void
    {
        $this->actingAs($this->csrStaff);

        $this->get('/reports/stock-on-hand/pdf')->assertStatus(403);
        $this->get('/reports/stock-on-hand/csv')->assertStatus(403);
        $this->get('/reports/near-expiry/pdf')->assertStatus(403);
        $this->get('/reports/deliveries/pdf')->assertStatus(403);
        $this->get('/reports/unit-requests/pdf')->assertStatus(403);
        $this->get('/reports/stock-movements/pdf')->assertStatus(403);
        $this->get('/reports/stock-movements/csv')->assertStatus(403);
    }

    public function test_stock_movements_pdf_and_csv_export_successfully(): void
    {
        $request = Request::where('request_number', 'REQ-REP-001')->first();
        $line = $request->lines()->first();

        app(FulfillmentService::class)->fulfillRequest($request, [
            [
                'line_id' => $line->id,
                'batch_id' => $this->batch->id,
                'quantity' => 25,
            ],
        ], $this->custodian);

        $this->actingAs($this->custodian);

        $this->get('/reports/stock-movements/pdf')
            ->assertStatus(200)
            ->assertHeader('Content-Type', 'application/pdf');

        $csvResponse = $this->get('/reports/stock-movements/csv');
        $csvResponse->assertStatus(200);
        $content = $csvResponse->streamedContent();
        $this->assertStringContainsString('LOT-REP-01', $content);
        $this->assertStringContainsString('REQ-REP-001', $content);
    }

    public function test_issue_slip_pdf_is_accessible_to_requesting_unit_and_never_displays_pricing(): void
    {
        $request = Request::where('request_number', 'REQ-REP-001')->first();
        $line = $request->lines()->first();

        app(FulfillmentService::class)->fulfillRequest($request, [
            [
                'line_id' => $line->id,
                'batch_id' => $this->batch->id,
                'quantity' => 25,
            ],
        ], $this->custodian);

        // Requesting CSR unit staff can download issue slip PDF
        $this->actingAs($this->csrStaff);
        $this->get("/reports/requests/{$request->id}/issue-slip")
            ->assertStatus(200)
            ->assertHeader('Content-Type', 'application/pdf');

        // Another unit's staff (HDU) cannot view CSR's issue slip
        $hduStaff = User::where('email', 'hdu@rmph.local')->first();
        $this->actingAs($hduStaff);
        $this->get("/reports/requests/{$request->id}/issue-slip")
            ->assertStatus(403);

        // HTML view for issue slip strictly omits unit cost / pricing
        $requisition = $request->fresh(['unit', 'requestedBy', 'fulfilledBy', 'lines.item']);
        $html = view('reports.issue-slip', [
            'requisition' => $requisition,
            'issuedMovements' => $requisition->getIssuedMovements(),
            'generatedAt' => Carbon::now('Asia/Manila')->format('F j, Y g:i A'),
            'generatedBy' => $this->custodian->name,
        ])->render();

        $this->assertStringContainsString('LOT-REP-01', $html);
        $this->assertStringNotContainsString('Unit Cost', $html);
        $this->assertStringNotContainsString('4.50', $html);
    }

    public function test_cost_columns_included_in_reports_for_custodian_and_scoped_by_category(): void
    {
        $drugItem = Item::create([
            'code' => 'DRG-REP-99',
            'description' => 'Amoxicillin 500mg Capsule',
            'category' => ItemCategory::Drug,
            'unit_of_measure' => 'box of 100',
            'consignor_id' => $this->item->consignor_id,
            'minimum_level' => 10,
            'is_active' => true,
        ]);

        Batch::create([
            'delivery_id' => $this->delivery->id,
            'item_id' => $drugItem->id,
            'lot_number' => 'LOT-DRG-REP',
            'expiration_date' => Carbon::today('Asia/Manila')->addDays(60),
            'quantity_received' => 50,
            'quantity_remaining' => 50,
            'unit_cost' => 120.00,
            'is_active' => true,
        ]);

        $this->actingAs($this->custodian); // custodian_supplies
        $content = $this->get('/reports/stock-on-hand/csv')->streamedContent();

        $this->assertStringContainsString('Avg. Unit Cost (PHP)', $content);
        $this->assertStringContainsString('Total Valuation (PHP)', $content);
        $this->assertStringContainsString('SUP-001', $content);
        $this->assertStringNotContainsString('DRG-REP-99', $content);
    }
}
