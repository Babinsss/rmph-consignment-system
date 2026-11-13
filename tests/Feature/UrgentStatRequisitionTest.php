<?php

namespace Tests\Feature;

use App\Enums\ItemCategory;
use App\Enums\RequestStatus;
use App\Filament\Widgets\UrgentRequestsAlertWidget;
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

class UrgentStatRequisitionTest extends TestCase
{
    use RefreshDatabase;

    protected User $custodian;

    protected User $hduStaff;

    protected Unit $hduUnit;

    protected Item $item;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(UnitSeeder::class);
        $this->seed(RoleAndPermissionSeeder::class);
        $this->seed(UserSeeder::class);

        $this->custodian = User::where('email', 'aliana.dula@rmph.local')->first();
        $this->hduStaff = User::where('email', 'hdu@rmph.local')->first();
        $this->hduUnit = Unit::where('code', 'HDU')->first();

        $consignor = Consignor::create([
            'name' => 'Metro Drug Inc.',
            'code' => 'METRODRUG',
            'is_active' => true,
        ]);

        $this->item = Item::create([
            'code' => 'SUP-002',
            'description' => 'IV Cannula G20 with injection port',
            'category' => ItemCategory::Supply,
            'unit_of_measure' => 'box of 50',
            'consignor_id' => $consignor->id,
            'minimum_level' => 10,
            'is_active' => true,
        ]);
    }

    public function test_urgent_stat_alert_widget_renders_when_pending_urgent_requests_exist(): void
    {
        Request::create([
            'request_number' => 'REQ-STAT-001',
            'unit_id' => $this->hduUnit->id,
            'requested_by_id' => $this->hduStaff->id,
            'request_date' => now(),
            'is_urgent' => true,
            'urgent_reason' => 'Emergency trauma patient requiring STAT IV line insertion.',
            'status' => RequestStatus::Pending,
        ]);

        $this->assertTrue(UrgentRequestsAlertWidget::canView());

        $this->actingAs($this->custodian);

        Livewire::test(UrgentRequestsAlertWidget::class)
            ->assertSuccessful()
            ->assertSee('URGENT (STAT) REQUISITION ALERT')
            ->assertSee('REQ-STAT-001')
            ->assertSee('High Dependency Unit (HDU)')
            ->assertSee('Emergency trauma patient requiring STAT IV line insertion.');
    }

    public function test_urgent_stat_alert_widget_is_hidden_when_no_urgent_requests_are_pending(): void
    {
        $this->assertFalse(UrgentRequestsAlertWidget::canView());
    }

    public function test_urgent_stat_request_can_be_submitted_at_midnight_on_sunday(): void
    {
        // Sunday 2:30 AM
        Carbon::setTestNow(Carbon::create(2026, 11, 15, 2, 30, 0, 'Asia/Manila'));

        $service = app(RequestService::class);
        $request = $service->submitRequest([
            'unit_id' => $this->hduUnit->id,
            'is_urgent' => true,
            'urgent_reason' => 'Patient in acute shock admitted at 2am; urgent fluid resuscitation supplies needed.',
            'lines' => [
                [
                    'item_id' => $this->item->id,
                    'quantity_requested' => 15,
                ],
            ],
        ], $this->hduStaff);

        $this->assertTrue($request->is_urgent);
        $this->assertEquals(RequestStatus::Pending, $request->status);
        $this->assertDatabaseHas('requests', [
            'id' => $request->id,
            'is_urgent' => true,
        ]);

        Carbon::setTestNow();
    }

    public function test_urgent_stat_request_fails_if_reason_is_frivolous_or_too_short(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 11, 15, 2, 30, 0, 'Asia/Manila'));

        $this->expectException(ValidationException::class);

        $service = app(RequestService::class);
        $service->submitRequest([
            'unit_id' => $this->hduUnit->id,
            'is_urgent' => true,
            'urgent_reason' => 'asap', // Only 4 characters, must be >= 10
            'lines' => [
                [
                    'item_id' => $this->item->id,
                    'quantity_requested' => 15,
                ],
            ],
        ], $this->hduStaff);

        Carbon::setTestNow();
    }

    public function test_operations_request_list_has_urgent_tab_and_shows_stat_request(): void
    {
        Request::create([
            'request_number' => 'REQ-STAT-002',
            'unit_id' => $this->hduUnit->id,
            'requested_by_id' => $this->hduStaff->id,
            'request_date' => now(),
            'is_urgent' => true,
            'urgent_reason' => 'Emergency cardio resuscitation cannula needed.',
            'status' => RequestStatus::Pending,
        ]);

        $this->actingAs($this->custodian);
        $response = $this->get('/admin/requests');

        $response->assertStatus(200);
        $response->assertSee('REQ-STAT-002');
        $response->assertSee('STAT (Urgent)');
    }

    public function test_urgent_stat_alert_widget_respects_custodian_category_scope(): void
    {
        $drugItem = Item::create([
            'code' => 'DRG-STAT-01',
            'description' => 'Epinephrine 1mg/mL Ampule',
            'category' => ItemCategory::Drug,
            'unit_of_measure' => 'ampule',
            'consignor_id' => $this->item->consignor_id,
            'minimum_level' => 5,
            'is_active' => true,
        ]);

        $drugStat = Request::create([
            'request_number' => 'REQ-STAT-DRG-01',
            'unit_id' => $this->hduUnit->id,
            'requested_by_id' => $this->hduStaff->id,
            'request_date' => now(),
            'is_urgent' => true,
            'urgent_reason' => 'Code blue emergency crash cart drug.',
            'status' => RequestStatus::Pending,
        ]);

        $drugStat->lines()->create([
            'item_id' => $drugItem->id,
            'quantity_requested' => 4,
            'quantity_issued' => 0,
        ]);

        $this->actingAs($this->custodian); // custodian_supplies
        $this->assertFalse(UrgentRequestsAlertWidget::canView());

        $drugCustodian = User::where('email', 'kristine.batan@rmph.local')->first();
        $this->actingAs($drugCustodian);
        $this->assertTrue(UrgentRequestsAlertWidget::canView());

        Livewire::test(UrgentRequestsAlertWidget::class)
            ->assertSee('REQ-STAT-DRG-01');
    }

    public function test_custodian_can_record_downtime_requisition_outside_regular_schedule_window(): void
    {
        // Saturday 11:00 PM (regular window is closed)
        Carbon::setTestNow(Carbon::create(2026, 11, 14, 23, 0, 0, 'Asia/Manila'));

        $request = app(RequestService::class)->submitDowntimeRequest([
            'unit_id' => $this->hduUnit->id,
            'downtime_reference' => 'RIS-PAPER-2026-88',
            'is_urgent' => false,
            'notes' => 'Recorded from manual paper slip after network outage.',
            'lines' => [
                [
                    'item_id' => $this->item->id,
                    'quantity_requested' => 12,
                ],
            ],
        ], $this->custodian);

        $this->assertTrue($request->is_downtime);
        $this->assertEquals('RIS-PAPER-2026-88', $request->downtime_reference);
        $this->assertEquals(RequestStatus::Pending, $request->status);

        Carbon::setTestNow();
    }
}
