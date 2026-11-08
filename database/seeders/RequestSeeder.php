<?php

namespace Database\Seeders;

use App\Enums\RequestStatus;
use App\Models\Item;
use App\Models\Request;
use App\Models\Unit;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class RequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Request::count() > 0) {
            return;
        }

        $now = Carbon::now('Asia/Manila');

        // 1. CSR Request
        $csrUnit = Unit::where('code', 'CSR')->first();
        $csrUser = User::where('email', 'csr@rmph.local')->first();
        $syringe = Item::where('code', 'SUP-001')->first();
        $cannula = Item::where('code', 'SUP-002')->first();

        if ($csrUnit && $csrUser && $syringe && $cannula) {
            $req1 = Request::create([
                'request_number' => 'REQ-2026-0001',
                'unit_id' => $csrUnit->id,
                'requested_by_id' => $csrUser->id,
                'request_date' => $now->copy()->subHours(2),
                'is_urgent' => false,
                'status' => RequestStatus::Pending,
                'notes' => 'Regular weekly ward replenishment for general surgery wards.',
            ]);

            $req1->lines()->createMany([
                [
                    'item_id' => $syringe->id,
                    'quantity_requested' => 150,
                    'quantity_issued' => 0,
                ],
                [
                    'item_id' => $cannula->id,
                    'quantity_requested' => 80,
                    'quantity_issued' => 0,
                ],
            ]);
        }

        // 2. Gugma Dialysis Request
        $dialysisUnit = Unit::where('code', 'DIALYSIS')->first();
        $dialysisUser = User::where('email', 'dialysis@rmph.local')->first();
        $dialyzer = Item::where('code', 'SUP-005')->first();

        if ($dialysisUnit && $dialysisUser && $dialyzer) {
            $req2 = Request::create([
                'request_number' => 'REQ-2026-0002',
                'unit_id' => $dialysisUnit->id,
                'requested_by_id' => $dialysisUser->id,
                'request_date' => $now->copy()->subHour(),
                'is_urgent' => false,
                'status' => RequestStatus::Pending,
                'notes' => 'Weekly supply for hemodialysis sessions.',
            ]);

            $req2->lines()->create([
                'item_id' => $dialyzer->id,
                'quantity_requested' => 30,
                'quantity_issued' => 0,
            ]);
        }

        // 3. HDU STAT Request
        $hduUnit = Unit::where('code', 'HDU')->first();
        $hduUser = User::where('email', 'hdu@rmph.local')->first();
        $paracetamol = Item::where('code', 'DRG-001')->first();

        if ($hduUnit && $hduUser && $paracetamol && $cannula) {
            $req3 = Request::create([
                'request_number' => 'REQ-2026-0003',
                'unit_id' => $hduUnit->id,
                'requested_by_id' => $hduUser->id,
                'request_date' => $now->copy()->subMinutes(25),
                'is_urgent' => true,
                'urgent_reason' => 'Emergency critical patient in HDU requiring STAT IV analgesic therapy.',
                'status' => RequestStatus::Pending,
                'notes' => 'Please expedite fulfillment.',
            ]);

            $req3->lines()->createMany([
                [
                    'item_id' => $paracetamol->id,
                    'quantity_requested' => 20,
                    'quantity_issued' => 0,
                ],
                [
                    'item_id' => $cannula->id,
                    'quantity_requested' => 25,
                    'quantity_issued' => 0,
                ],
            ]);
        }
    }
}
