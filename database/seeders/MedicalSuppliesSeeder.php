<?php

namespace Database\Seeders;

use App\Models\Delivery;
use App\Models\DeliveryItem;
use App\Models\Item;
use App\Models\Supplier;
use App\Models\User;
use App\Services\InventoryService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class MedicalSuppliesSeeder extends Seeder
{
    /**
     * Seed the real Roxas Memorial Provincial Hospital (RMPH) consignors and medical supply catalog.
     */
    public function run(): void
    {
        $inventoryService = new InventoryService;
        $medSupplyStaff = User::where('role', 'medical_supply_staff')->first()
            ?? User::where('role', 'admin')->first();

        // 1. Seed 16 Real Consignors
        $consignorsPath = database_path('data/consignors.json');
        if (File::exists($consignorsPath)) {
            $consignorsData = json_decode(File::get($consignorsPath), true);
            foreach ($consignorsData as $data) {
                Supplier::updateOrCreate(
                    ['name' => $data['name']],
                    [
                        'code' => $data['code'],
                        'contact_person' => $data['contact_person'],
                        'email' => $data['email'],
                        'phone' => $data['phone'],
                        'address' => $data['address'],
                        'consignment_type' => 'medical_supplies',
                        'payment_terms' => $data['payment_terms'],
                        'is_active' => true,
                    ]
                );
            }
        }

        // Cache suppliers by name for fast lookup
        $suppliersByName = Supplier::all()->keyBy('name');

        // 2. Seed 306 Real Medical Supply Items
        $itemsPath = database_path('data/medical_supplies_items.json');
        if (File::exists($itemsPath)) {
            $itemsData = json_decode(File::get($itemsPath), true);
            foreach ($itemsData as $it) {
                $supplier = $suppliersByName->get($it['consignor']);

                Item::updateOrCreate(
                    ['item_code' => $it['item_code']],
                    [
                        'name' => $it['name'],
                        'department' => 'medical_supplies',
                        'category' => $it['category'],
                        'uom' => $it['uom'],
                        'supplier_id' => $supplier?->id,
                        'minimum_stock_level' => $it['minimum_stock_level'],
                        'unit_cost' => $it['unit_cost'],
                        'selling_price' => $it['selling_price'],
                        'is_active' => true,
                    ]
                );
            }
        }

        // 3. Seed Inward Deliveries from real records in CONSIGNMENT-LEDGER_CU-2025
        $mhjSupplier = Supplier::where('name', 'like', '%MHJ%')->first();
        if ($mhjSupplier) {
            $crDelivery = Delivery::firstOrCreate(
                ['delivery_number' => 'CR-01-001-2025'],
                [
                    'supplier_id' => $mhjSupplier->id,
                    'department' => 'medical_supplies',
                    'delivery_date' => Carbon::parse('2025-03-24'),
                    'received_by_id' => $medSupplyStaff?->id,
                    'po_reference' => 'SPR/CO No: CR-01-001-2025',
                    'total_cost' => 0.00,
                    'status' => 'received',
                    'notes' => 'Official RMPH Consignment Receipt from MHJ Medical and Laboratory Supply',
                ]
            );

            if ($crDelivery->items()->count() === 0) {
                // Key items from the real Excel delivery sheet:
                $itemsToDeliver = [
                    ['code' => 'HECSM-RMH-CU01', 'lot' => 'LOT-IND-7025', 'qty' => 5000, 'cost' => 20.00, 'months' => 24],
                    ['code' => 'HECSM-RMH-CU02', 'lot' => 'LOT-IND-7525', 'qty' => 5000, 'cost' => 20.00, 'months' => 24],
                    ['code' => 'HECSM-RMH-CU03', 'lot' => 'LOT-SYR-1CC25', 'qty' => 14500, 'cost' => 2.50, 'months' => 36],
                    ['code' => 'HECSM-RMH-CU04', 'lot' => 'LOT-SYR-3CC25', 'qty' => 15000, 'cost' => 2.80, 'months' => 36],
                    ['code' => 'HECSM-RMH-CU05', 'lot' => 'LOT-SYR-5CC25', 'qty' => 15000, 'cost' => 2.80, 'months' => 36],
                    ['code' => 'HECSM-RMH-CU06', 'lot' => 'LOT-SYR-10CC25', 'qty' => 14000, 'cost' => 4.00, 'months' => 36],
                    ['code' => 'HECSM-RMH-CU07', 'lot' => 'LOT-ALC-7025', 'qty' => 1000, 'cost' => 68.00, 'months' => 18],
                    ['code' => 'HECSM-RMH-CU08', 'lot' => 'LOT-SYR-50CC25', 'qty' => 750, 'cost' => 30.00, 'months' => 36],
                    ['code' => 'HECSM-RMH-CU09', 'lot' => 'LOT-IND-6525', 'qty' => 3300, 'cost' => 20.00, 'months' => 24],
                    ['code' => 'HECSM-RMH-CU025', 'lot' => 'LOT-FOL-1625', 'qty' => 1200, 'cost' => 40.00, 'months' => 30],
                ];

                foreach ($itemsToDeliver as $batchData) {
                    $item = Item::where('item_code', $batchData['code'])->first();
                    if ($item) {
                        DeliveryItem::create([
                            'delivery_id' => $crDelivery->id,
                            'item_id' => $item->id,
                            'lot_number' => $batchData['lot'],
                            'expiry_date' => Carbon::now()->addMonths($batchData['months'])->toDateString(),
                            'quantity_received' => $batchData['qty'],
                            'quantity_remaining' => $batchData['qty'],
                            'unit_cost' => $batchData['cost'],
                            'total_cost' => $batchData['qty'] * $batchData['cost'],
                            'status' => 'available',
                        ]);
                    }
                }

                $inventoryService->processInwardDelivery($crDelivery, $medSupplyStaff);
            }
        }
    }
}
