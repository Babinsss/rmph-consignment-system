<?php

namespace Database\Seeders;

use App\Models\Consignor;
use App\Models\Delivery;
use App\Models\Item;
use App\Models\User;
use App\Services\DeliveryService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DeliverySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Don't duplicate if deliveries exist
        if (Delivery::count() > 0) {
            return;
        }

        $deliveryService = app(DeliveryService::class);
        $custodian = User::where('email', 'aliana.dula@rmph.local')->first()
            ?? User::first();

        $today = Carbon::today('Asia/Manila');

        // Delivery 1: Zuellig Pharma (Syringes + Paracetamol IV)
        $zuellig = Consignor::where('name', 'like', '%Zuellig%')->first();
        $syringe = Item::where('code', 'SUP-001')->first();
        $paracetamol = Item::where('code', 'DRG-001')->first();

        if ($zuellig && $syringe && $paracetamol) {
            $deliveryService->recordDelivery([
                'consignor_id' => $zuellig->id,
                'dr_number' => 'DR-ZP-2026-0814',
                'delivery_date' => $today->copy()->subDays(5),
                'received_by_id' => $custodian->id,
                'notes' => 'Regular weekly consignment replenishment for CSR and Pharmacy.',
                'batches' => [
                    [
                        'item_id' => $syringe->id,
                        'lot_number' => 'LOT-SYR-2026-01',
                        'expiration_date' => $today->copy()->addMonths(18),
                        'quantity_received' => 2000,
                        'unit_cost' => 4.50,
                    ],
                    [
                        'item_id' => $paracetamol->id,
                        'lot_number' => 'LOT-PCM-2026-92',
                        'expiration_date' => $today->copy()->addMonths(12),
                        'quantity_received' => 300,
                        'unit_cost' => 45.00,
                    ],
                ],
            ]);
        }

        // Delivery 2: Metro Drug (IV Cannulas + Cefuroxime)
        $metroDrug = Consignor::where('name', 'like', '%Metro Drug%')->first();
        $cannula = Item::where('code', 'SUP-002')->first();
        $cefuroxime = Item::where('code', 'DRG-002')->first();

        if ($metroDrug && $cannula && $cefuroxime) {
            $deliveryService->recordDelivery([
                'consignor_id' => $metroDrug->id,
                'dr_number' => 'DR-MD-2026-1190',
                'delivery_date' => $today->copy()->subDays(3),
                'received_by_id' => $custodian->id,
                'notes' => 'Received in good condition, stored in Central Depot.',
                'batches' => [
                    [
                        'item_id' => $cannula->id,
                        'lot_number' => 'LOT-CAN-2026-44',
                        'expiration_date' => $today->copy()->addMonths(24),
                        'quantity_received' => 800,
                        'unit_cost' => 18.25,
                    ],
                    [
                        'item_id' => $cefuroxime->id,
                        'lot_number' => 'LOT-CEF-2026-10',
                        'expiration_date' => $today->copy()->addDays(60), // Near expiry (within 90 days) for testing
                        'quantity_received' => 150,
                        'unit_cost' => 85.00,
                    ],
                ],
            ]);
        }

        // Delivery 3: Fresenius Medical Care (Dialyzers for Gugma Dialysis)
        $fresenius = Consignor::where('name', 'like', '%Fresenius%')->first();
        $dialyzer = Item::where('code', 'SUP-005')->first();

        if ($fresenius && $dialyzer) {
            $deliveryService->recordDelivery([
                'consignor_id' => $fresenius->id,
                'dr_number' => 'DR-FMC-2026-0305',
                'delivery_date' => $today->copy()->subDays(1),
                'received_by_id' => $custodian->id,
                'notes' => 'Consignment batch for Gugma Dialysis unit.',
                'batches' => [
                    [
                        'item_id' => $dialyzer->id,
                        'lot_number' => 'LOT-DIA-2026-08',
                        'expiration_date' => $today->copy()->addMonths(30),
                        'quantity_received' => 120,
                        'unit_cost' => 950.00,
                    ],
                ],
            ]);
        }
    }
}
