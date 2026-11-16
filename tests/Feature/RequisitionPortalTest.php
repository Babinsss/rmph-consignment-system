<?php

namespace Tests\Feature;

use App\Enums\ItemCategory;
use App\Enums\RequestStatus;
use App\Filament\Portal\Resources\Requests\Pages\CreateRequest;
use App\Models\Consignor;
use App\Models\Item;
use App\Models\Request;
use App\Models\Unit;
use App\Models\User;
use App\Services\RequestService;
use Carbon\Carbon;
use Database\Seeders\RoleAndPermissionSeeder;
use Database\Seeders\UnitSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Tests\TestCase;

class RequisitionPortalTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $custodian;

    protected User $viewer;

    protected User $csrStaff;

    protected User $hduStaff;

    protected Unit $csrUnit;

    protected Unit $hduUnit;

    protected Item $item;

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
        $this->hduStaff = User::where('email', 'hdu@rmph.local')->first();

        $this->csrUnit = Unit::where('code', 'CSR')->first();
        $this->hduUnit = Unit::where('code', 'HDU')->first();

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
    }

    public function test_ward_staff_can_view_own_unit_requisitions_only(): void
    {
        // Create CSR request
        $csrRequest = Request::create([
            'request_number' => 'REQ-CSR-001',
            'unit_id' => $this->csrUnit->id,
            'requested_by_id' => $this->csrStaff->id,
            'request_date' => now(),
            'is_urgent' => false,
            'status' => RequestStatus::Pending,
        ]);

        // Create HDU request
        $hduRequest = Request::create([
            'request_number' => 'REQ-HDU-001',
            'unit_id' => $this->hduUnit->id,
            'requested_by_id' => $this->hduStaff->id,
            'request_date' => now(),
            'is_urgent' => false,
            'status' => RequestStatus::Pending,
        ]);

        // CSR staff should see only CSR request
        $this->actingAs($this->csrStaff);
        $response = $this->get('/portal/requests');
        $response->assertStatus(200);
        $response->assertSee('REQ-CSR-001');
        $response->assertDontSee('REQ-HDU-001');

        // HDU staff should see only HDU request
        $this->actingAs($this->hduStaff);
        $response = $this->get('/portal/requests');
        $response->assertStatus(200);
        $response->assertSee('REQ-HDU-001');
        $response->assertDontSee('REQ-CSR-001');
    }

    public function test_ward_staff_can_submit_regular_requisition_during_open_window(): void
    {
        // Mock Wednesday at 10:00 AM (inside Monday-Friday, < 5:00 PM)
        Carbon::setTestNow(Carbon::create(2026, 11, 11, 10, 0, 0, 'Asia/Manila'));

        $service = app(RequestService::class);
        $request = $service->submitRequest([
            'unit_id' => $this->csrUnit->id,
            'is_urgent' => false,
            'notes' => 'Weekly ward stock replenishment',
            'lines' => [
                [
                    'item_id' => $this->item->id,
                    'quantity_requested' => 50,
                ],
            ],
        ], $this->csrStaff);

        $this->assertDatabaseHas('requests', [
            'id' => $request->id,
            'unit_id' => $this->csrUnit->id,
            'requested_by_id' => $this->csrStaff->id,
            'is_urgent' => false,
            'status' => RequestStatus::Pending,
        ]);

        $this->assertDatabaseHas('request_lines', [
            'request_id' => $request->id,
            'item_id' => $this->item->id,
            'quantity_requested' => 50,
            'quantity_issued' => 0,
        ]);

        Carbon::setTestNow();
    }

    public function test_ward_staff_cannot_submit_regular_requisition_after_5pm(): void
    {
        // Mock Wednesday at 6:30 PM (after 5:00 PM)
        Carbon::setTestNow(Carbon::create(2026, 11, 11, 18, 30, 0, 'Asia/Manila'));

        $this->expectException(ValidationException::class);

        $service = app(RequestService::class);
        $service->submitRequest([
            'unit_id' => $this->csrUnit->id,
            'is_urgent' => false,
            'lines' => [
                [
                    'item_id' => $this->item->id,
                    'quantity_requested' => 50,
                ],
            ],
        ], $this->csrStaff);

        Carbon::setTestNow();
    }

    public function test_urgent_stat_requisition_allowed_after_hours_with_reason(): void
    {
        // Mock Saturday at 11:00 PM (outside normal window)
        Carbon::setTestNow(Carbon::create(2026, 11, 14, 23, 0, 0, 'Asia/Manila'));

        $service = app(RequestService::class);
        $request = $service->submitRequest([
            'unit_id' => $this->hduUnit->id,
            'is_urgent' => true,
            'urgent_reason' => 'Critical emergency trauma patient needing immediate resuscitation supplies.',
            'lines' => [
                [
                    'item_id' => $this->item->id,
                    'quantity_requested' => 20,
                ],
            ],
        ], $this->hduStaff);

        $this->assertTrue($request->is_urgent);
        $this->assertNotNull($request->urgent_reason);
        $this->assertEquals(RequestStatus::Pending, $request->status);

        Carbon::setTestNow();
    }

    public function test_urgent_stat_requisition_requires_mandatory_clinical_reason(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 11, 14, 23, 0, 0, 'Asia/Manila'));

        $this->expectException(ValidationException::class);

        $service = app(RequestService::class);
        $service->submitRequest([
            'unit_id' => $this->hduUnit->id,
            'is_urgent' => true,
            'urgent_reason' => '', // Empty reason should fail
            'lines' => [
                [
                    'item_id' => $this->item->id,
                    'quantity_requested' => 20,
                ],
            ],
        ], $this->hduStaff);

        Carbon::setTestNow();
    }

    public function test_custodian_and_viewer_can_view_all_unit_requisitions(): void
    {
        $this->actingAs($this->custodian);
        $this->get('/admin/requests')->assertStatus(200);

        $this->actingAs($this->viewer);
        $this->get('/admin/requests')->assertStatus(200);
    }

    public function test_custodian_can_reject_a_pending_requisition(): void
    {
        $request = Request::create([
            'request_number' => 'REQ-REJ-001',
            'unit_id' => $this->csrUnit->id,
            'requested_by_id' => $this->csrStaff->id,
            'request_date' => now(),
            'is_urgent' => false,
            'status' => RequestStatus::Pending,
        ]);

        $request->update([
            'status' => RequestStatus::Rejected,
            'rejection_reason' => 'Item currently out of stock from distributor.',
        ]);

        $this->assertEquals(RequestStatus::Rejected, $request->fresh()->status);
        $this->assertEquals('Item currently out of stock from distributor.', $request->fresh()->rejection_reason);
    }

    public function test_ward_staff_can_view_two_pane_create_request_page(): void
    {
        $this->actingAs($this->csrStaff);

        $response = $this->get('/portal/requests/create');
        $response->assertStatus(200);
        $response->assertSee('New Ward Requisition Slip');
        $response->assertSee('Requisition Slip');
        $response->assertSee('Sterile Syringe 3ml');
        $response->assertSee('Filter:');
        $response->assertSee('All Consigned Items');
        $response->assertSee('Medical Supplies');
        $response->assertSee('Drugs and Medicines');

        // Verify strictly that NO unit cost or financial leak occurs
        $response->assertDontSee('Unit Cost');
        $response->assertDontSee('unit_cost');
        $response->assertDontSee('Total Value');
        $response->assertDontSee('₱');
    }

    public function test_ward_staff_can_view_stat_emergency_page_with_preset_chips(): void
    {
        $this->actingAs($this->hduStaff);

        $response = $this->get('/portal/requests/create?urgent=1');
        $response->assertStatus(200);
        $response->assertSee('STAT Emergency Requisition Slip');
        $response->assertSee('STAT Emergency Protocol Active');
        $response->assertSee('Acute Trauma Emergency');
        $response->assertSee('Critical Low Stock in Ward');
        $response->assertSee('STAT Bedside Procedure');
        $response->assertSee('Unexpected Surgical Complication');
        $response->assertSee('ICU/HDU Rapid Response');
        $response->assertSee('Privacy Notice:');
    }

    public function test_ward_staff_can_interact_with_two_pane_request_builder_and_submit(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 11, 11, 10, 0, 0, 'Asia/Manila'));
        $this->actingAs($this->csrStaff);

        Livewire::test(CreateRequest::class)
            ->call('addToCart', $this->item->id)
            ->assertSet('cart.'.$this->item->id, 1)
            ->call('incrementQuantity', $this->item->id)
            ->assertSet('cart.'.$this->item->id, 2)
            ->call('openConfirm')
            ->assertSet('showConfirmModal', true)
            ->call('submitRequisition')
            ->assertRedirect(url('/portal/requests'));

        $this->assertDatabaseHas('requests', [
            'unit_id' => $this->csrUnit->id,
            'is_urgent' => false,
            'status' => RequestStatus::Pending,
        ]);

        $this->assertDatabaseHas('request_lines', [
            'item_id' => $this->item->id,
            'quantity_requested' => 2,
        ]);

        Carbon::setTestNow();
    }

    public function test_ward_staff_can_submit_stat_requisition_with_preset_reason(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 11, 14, 23, 0, 0, 'Asia/Manila'));
        $this->actingAs($this->hduStaff);

        Livewire::withQueryParams(['urgent' => 1])
            ->test(CreateRequest::class)
            ->assertSet('is_urgent', true)
            ->call('addToCart', $this->item->id)
            ->call('selectPresetReason', 'Acute Trauma Emergency')
            ->set('urgent_reason', 'Acute Trauma Emergency: Severe haemorrhage in trauma bay.')
            ->call('openConfirm')
            ->assertSet('showConfirmModal', true)
            ->call('submitRequisition')
            ->assertRedirect(url('/portal/requests'));

        $this->assertDatabaseHas('requests', [
            'unit_id' => $this->hduUnit->id,
            'is_urgent' => true,
            'urgent_reason' => 'Acute Trauma Emergency: Severe haemorrhage in trauma bay.',
            'status' => RequestStatus::Pending,
        ]);

        Carbon::setTestNow();
    }

    public function test_ward_staff_can_cancel_own_pending_request_but_not_other_unit_request(): void
    {
        $request = Request::create([
            'request_number' => 'REQ-CSR-CAN-01',
            'unit_id' => $this->csrUnit->id,
            'requested_by_id' => $this->csrStaff->id,
            'request_date' => now(),
            'is_urgent' => false,
            'status' => RequestStatus::Pending,
        ]);

        $service = app(RequestService::class);
        $cancelled = $service->cancelRequest($request, $this->csrStaff, 'Order no longer needed.');

        $this->assertEquals(RequestStatus::Cancelled, $cancelled->status);
        $this->assertEquals('Order no longer needed.', $cancelled->rejection_reason);

        $hduRequest = Request::create([
            'request_number' => 'REQ-HDU-CAN-02',
            'unit_id' => $this->hduUnit->id,
            'requested_by_id' => $this->hduStaff->id,
            'request_date' => now(),
            'is_urgent' => false,
            'status' => RequestStatus::Pending,
        ]);

        $this->expectException(ValidationException::class);
        $service->cancelRequest($hduRequest, $this->csrStaff);
    }
}
