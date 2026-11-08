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
    }
}
