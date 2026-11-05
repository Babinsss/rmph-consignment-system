<?php

namespace Database\Seeders;

use App\Models\Delivery;
use App\Models\DeliveryItem;
use App\Models\Item;
use App\Models\StockRequest;
use App\Models\StockRequestItem;
use App\Models\Supplier;
use App\Models\User;
use App\Services\InventoryService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $inventoryService = new InventoryService;

        // 1. Official RMPH Consignment Leadership & Officers
        $admin = User::firstOrCreate(
            ['email' => 'admin@rmph.gov.ph'],
            [
                'name' => 'Florencio O. Luching Jr., M.D., FPCS',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'department' => 'all',
                'is_active' => true,
            ]
        );

        $coh = User::firstOrCreate(
            ['email' => 'coh@rmph.gov.ph'],
            [
                'name' => 'Florencio O. Luching Jr., M.D., FPCS',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'department' => 'all',
                'is_active' => true,
            ]
        );

        // Consignment Officer for Pharmacy Stocks
        $pharmacyStaff = User::firstOrCreate(
            ['email' => 'kristine.batan@rmph.gov.ph'],
            [
                'name' => 'Kristine Mae Batan',
                'password' => Hash::make('password'),
                'role' => 'pharmacy_staff',
                'department' => 'pharmacy',
                'is_active' => true,
            ]
        );

        // Keep alias for easy login
        User::firstOrCreate(
            ['email' => 'pharmacy@rmph.gov.ph'],
            [
                'name' => 'Kristine Mae Batan',
                'password' => Hash::make('password'),
                'role' => 'pharmacy_staff',
                'department' => 'pharmacy',
                'is_active' => true,
            ]
        );

        // Consignment Officer for Medical Supplies
        $medSupplyStaff = User::firstOrCreate(
            ['email' => 'aliana.dula@rmph.gov.ph'],
            [
                'name' => 'Aliana Marie Dula',
                'password' => Hash::make('password'),
                'role' => 'medical_supply_staff',
                'department' => 'medical_supplies',
                'is_active' => true,
            ]
        );

        // Keep alias for easy login
        User::firstOrCreate(
            ['email' => 'medsupply@rmph.gov.ph'],
            [
                'name' => 'Aliana Marie Dula',
                'password' => Hash::make('password'),
                'role' => 'medical_supply_staff',
                'department' => 'medical_supplies',
                'is_active' => true,
            ]
        );

        // Authorized Unit Requesters
        $requester = User::firstOrCreate(
            ['email' => 'csr.nurse@rmph.gov.ph'],
            [
                'name' => 'Mia Buenvenida, RN',
                'password' => Hash::make('password'),
                'role' => 'department_requester',
                'department' => 'CSR',
                'is_active' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'hdu.nurse@rmph.gov.ph'],
            [
                'name' => 'HDU Staff Nurse',
                'password' => Hash::make('password'),
                'role' => 'department_requester',
                'department' => 'HDU',
                'is_active' => true,
            ]
        );

        // 2. Suppliers
        $metroPharma = Supplier::firstOrCreate(
            ['code' => 'SUP-METRO'],
            [
                'name' => 'Metro Pharma Distributors Inc.',
                'contact_person' => 'Robert Tan',
                'email' => 'sales@metropharma.ph',
                'phone' => '+63 917 111 2233',
                'address' => '78 Medical Avenue, Quezon City',
                'consignment_type' => 'pharmacy',
                'payment_terms' => '30 days post-consumption',
                'is_active' => true,
            ]
        );

        $mediLife = Supplier::firstOrCreate(
            ['code' => 'SUP-MEDILIFE'],
            [
                'name' => 'MediLife Surgical & Health Solutions',
                'contact_person' => 'Cynthia Lim',
                'email' => 'orders@medilife.com.ph',
                'phone' => '+63 918 333 4455',
                'address' => '12 Healthcare Blvd, Pasig City',
                'consignment_type' => 'medical_supplies',
                'payment_terms' => '45 days post-consumption',
                'is_active' => true,
            ]
        );

        $apexGlobal = Supplier::firstOrCreate(
            ['code' => 'SUP-APEX'],
            [
                'name' => 'Apex Global Healthcare Supplies',
                'contact_person' => 'David Morales',
                'email' => 'contact@apexhealthcare.ph',
                'phone' => '+63 920 555 7788',
                'address' => '400 Hospital Way, Manila',
                'consignment_type' => 'both',
                'payment_terms' => '30 days post-consumption',
                'is_active' => true,
            ]
        );

        // 3. Catalog Items
        // Pharmacy Items
        $paracetamol = Item::firstOrCreate(
            ['item_code' => 'PHAR-PAR-500'],
            [
                'name' => 'Paracetamol 500mg/2ml Ampoule',
                'generic_name' => 'Paracetamol',
                'brand_name' => 'Biogesic IV',
                'description' => 'Injectable antipyretic/analgesic for inpatient ward use',
                'department' => 'pharmacy',
                'category' => 'Analgesics / Antipyretics',
                'uom' => 'ampoule',
                'supplier_id' => $metroPharma->id,
                'minimum_stock_level' => 50,
                'unit_cost' => 18.50,
                'selling_price' => 25.00,
                'is_active' => true,
            ]
        );

        $cefuroxime = Item::firstOrCreate(
            ['item_code' => 'PHAR-CEF-750'],
            [
                'name' => 'Cefuroxime 750mg Powder for Injection Vial',
                'generic_name' => 'Cefuroxime Sodium',
                'brand_name' => 'Zinacef',
                'description' => 'Second-generation cephalosporin antibiotic',
                'department' => 'pharmacy',
                'category' => 'Antibiotics',
                'uom' => 'vial',
                'supplier_id' => $metroPharma->id,
                'minimum_stock_level' => 30,
                'unit_cost' => 125.00,
                'selling_price' => 165.00,
                'is_active' => true,
            ]
        );

        $omeprazole = Item::firstOrCreate(
            ['item_code' => 'PHAR-OME-40'],
            [
                'name' => 'Omeprazole 40mg IV Vial',
                'generic_name' => 'Omeprazole',
                'brand_name' => 'Losec IV',
                'description' => 'Proton pump inhibitor with 10ml solvent',
                'department' => 'pharmacy',
                'category' => 'Gastrointestinal',
                'uom' => 'vial',
                'supplier_id' => $apexGlobal->id,
                'minimum_stock_level' => 25,
                'unit_cost' => 95.00,
                'selling_price' => 130.00,
                'is_active' => true,
            ]
        );

        // Medical Supplies Items
        $gloves = Item::firstOrCreate(
            ['item_code' => 'MED-GLV-070'],
            [
                'name' => 'Sterile Surgical Latex Gloves Size 7.0',
                'generic_name' => 'Surgical Gloves Powder-Free',
                'brand_name' => 'Protex SurgiGrip',
                'description' => 'Gamma-sterilized anatomical fit surgical gloves, 50 pairs/box',
                'department' => 'medical_supplies',
                'category' => 'PPE & Gloves',
                'uom' => 'pair',
                'supplier_id' => $mediLife->id,
                'minimum_stock_level' => 100,
                'unit_cost' => 45.00,
                'selling_price' => 60.00,
                'is_active' => true,
            ]
        );

        $cannula = Item::firstOrCreate(
            ['item_code' => 'MED-CAN-020'],
            [
                'name' => 'IV Cannula Gauge 20 with Injection Port',
                'generic_name' => 'Intravenous Catheter G20',
                'brand_name' => 'Venofix',
                'description' => 'Pink radiopaque PTFE catheter with flashback chamber',
                'department' => 'medical_supplies',
                'category' => 'Vascular Access',
                'uom' => 'piece',
                'supplier_id' => $mediLife->id,
                'minimum_stock_level' => 80,
                'unit_cost' => 32.00,
                'selling_price' => 48.00,
                'is_active' => true,
            ]
        );

        $gauze = Item::firstOrCreate(
            ['item_code' => 'MED-GAU-4X4'],
            [
                'name' => 'Sterile Gauze Sponge 4x4 (Pack of 5)',
                'generic_name' => 'Woven Cotton Gauze 8-Ply',
                'brand_name' => 'Dermasorb',
                'description' => 'Highly absorbent USP Type VII cotton gauze pads',
                'department' => 'medical_supplies',
                'category' => 'Wound Care',
                'uom' => 'pack',
                'supplier_id' => $apexGlobal->id,
                'minimum_stock_level' => 150,
                'unit_cost' => 28.00,
                'selling_price' => 38.00,
                'is_active' => true,
            ]
        );

        // 4. Inward Deliveries
        // Pharmacy Delivery
        $pharmDelivery = Delivery::firstOrCreate(
            ['delivery_number' => 'DR-2026-0101'],
            [
                'supplier_id' => $metroPharma->id,
                'department' => 'pharmacy',
                'delivery_date' => Carbon::now()->subDays(10),
                'received_by_id' => $pharmacyStaff->id,
                'po_reference' => 'PO-PHAR-2026-044',
                'total_cost' => 0.00,
                'status' => 'received',
                'notes' => 'Monthly consignment replenishment for inpatient pharmacy',
            ]
        );

        if ($pharmDelivery->items()->count() === 0) {
            DeliveryItem::create([
                'delivery_id' => $pharmDelivery->id,
                'item_id' => $paracetamol->id,
                'lot_number' => 'LOT-PAR-2601',
                'expiry_date' => Carbon::now()->addMonths(14),
                'quantity_received' => 200,
                'quantity_remaining' => 200,
                'unit_cost' => 18.50,
                'total_cost' => 3700.00,
                'status' => 'available',
            ]);

            DeliveryItem::create([
                'delivery_id' => $pharmDelivery->id,
                'item_id' => $cefuroxime->id,
                'lot_number' => 'LOT-CEF-2599',
                'expiry_date' => Carbon::now()->addMonths(8),
                'quantity_received' => 100,
                'quantity_remaining' => 100,
                'unit_cost' => 125.00,
                'total_cost' => 12500.00,
                'status' => 'available',
            ]);

            $inventoryService->processInwardDelivery($pharmDelivery, $pharmacyStaff);
        }

        // Medical Supplies Delivery
        $medDelivery = Delivery::firstOrCreate(
            ['delivery_number' => 'DR-2026-0201'],
            [
                'supplier_id' => $mediLife->id,
                'department' => 'medical_supplies',
                'delivery_date' => Carbon::now()->subDays(7),
                'received_by_id' => $medSupplyStaff->id,
                'po_reference' => 'PO-MED-2026-018',
                'total_cost' => 0.00,
                'status' => 'received',
                'notes' => 'Central CSR consumable restocking',
            ]
        );

        if ($medDelivery->items()->count() === 0) {
            DeliveryItem::create([
                'delivery_id' => $medDelivery->id,
                'item_id' => $gloves->id,
                'lot_number' => 'LOT-GLV-8801',
                'expiry_date' => Carbon::now()->addMonths(24),
                'quantity_received' => 300,
                'quantity_remaining' => 300,
                'unit_cost' => 45.00,
                'total_cost' => 13500.00,
                'status' => 'available',
            ]);

            DeliveryItem::create([
                'delivery_id' => $medDelivery->id,
                'item_id' => $cannula->id,
                'lot_number' => 'LOT-CAN-7722',
                'expiry_date' => Carbon::now()->addMonths(18),
                'quantity_received' => 250,
                'quantity_remaining' => 250,
                'unit_cost' => 32.00,
                'total_cost' => 8000.00,
                'status' => 'available',
            ]);

            $inventoryService->processInwardDelivery($medDelivery, $medSupplyStaff);
        }

        // 5. Sample Stock Requests
        // Request 1: Issued Pharmacy Request to HDU
        $issuedRequest = StockRequest::firstOrCreate(
            ['request_number' => 'RIS-2026-0001'],
            [
                'department' => 'pharmacy',
                'requesting_unit' => 'HDU',
                'requester_id' => $requester->id,
                'requester_name' => 'HDU Staff Nurse',
                'request_date' => Carbon::now()->subDays(3),
                'status' => 'approved',
                'approved_by_id' => $pharmacyStaff->id,
                'approved_at' => Carbon::now()->subDays(3),
                'purpose' => 'HDU High Dependency Unit Bedside Replenishment',
                'patient_mrn' => 'HDU-WARD-STOCK',
                'notes' => 'Stat issuance requested for HDU critical care patients',
            ]
        );

        if ($issuedRequest->items()->count() === 0) {
            StockRequestItem::create([
                'stock_request_id' => $issuedRequest->id,
                'item_id' => $paracetamol->id,
                'quantity_requested' => 30,
                'quantity_issued' => 0,
                'unit_cost' => 18.50,
                'total_cost' => 0.00,
            ]);

            StockRequestItem::create([
                'stock_request_id' => $issuedRequest->id,
                'item_id' => $cefuroxime->id,
                'quantity_requested' => 15,
                'quantity_issued' => 0,
                'unit_cost' => 125.00,
                'total_cost' => 0.00,
            ]);

            $inventoryService->issueRequest($issuedRequest, $pharmacyStaff);
        }

        // Request 2: Pending Medical Supplies Request from CSR
        $pendingRequest = StockRequest::firstOrCreate(
            ['request_number' => 'RIS-2026-0002'],
            [
                'department' => 'medical_supplies',
                'requesting_unit' => 'CSR',
                'requester_id' => $requester->id,
                'requester_name' => 'Mia Buenvenida, RN',
                'request_date' => Carbon::now()->subDay(),
                'status' => 'pending',
                'purpose' => 'Consignment replenishment for Central Supply Room',
                'patient_mrn' => 'CSR-RESTOCK',
                'notes' => 'Awaiting confirmation from central CSR custodian',
            ]
        );

        if ($pendingRequest->items()->count() === 0) {
            StockRequestItem::create([
                'stock_request_id' => $pendingRequest->id,
                'item_id' => $gloves->id,
                'quantity_requested' => 50,
                'quantity_issued' => 0,
                'unit_cost' => 45.00,
                'total_cost' => 0.00,
            ]);

            StockRequestItem::create([
                'stock_request_id' => $pendingRequest->id,
                'item_id' => $cannula->id,
                'quantity_requested' => 40,
                'quantity_issued' => 0,
                'unit_cost' => 32.00,
                'total_cost' => 0.00,
            ]);
        }

        // 6. Seed Complete RMPH Medical Supplies Consignment Catalog & Delivery
        $this->call(MedicalSuppliesSeeder::class);
    }
}
