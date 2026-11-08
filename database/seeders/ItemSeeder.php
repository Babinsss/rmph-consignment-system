<?php

namespace Database\Seeders;

use App\Enums\ItemCategory;
use App\Models\Consignor;
use App\Models\Item;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $consignors = Consignor::pluck('id', 'code');

        $items = [
            // Medical Supplies
            [
                'code' => 'SUP-001',
                'description' => 'Sterile Disposable Syringe 1ml with needle',
                'category' => ItemCategory::Supply,
                'unit_of_measure' => 'box of 100',
                'consignor_code' => 'TERUMO',
                'minimum_level' => 20,
            ],
            [
                'code' => 'SUP-002',
                'description' => 'Sterile Disposable Syringe 3ml with needle',
                'category' => ItemCategory::Supply,
                'unit_of_measure' => 'box of 100',
                'consignor_code' => 'TERUMO',
                'minimum_level' => 30,
            ],
            [
                'code' => 'SUP-003',
                'description' => 'Sterile Disposable Syringe 5ml with needle',
                'category' => ItemCategory::Supply,
                'unit_of_measure' => 'box of 100',
                'consignor_code' => 'TERUMO',
                'minimum_level' => 25,
            ],
            [
                'code' => 'SUP-004',
                'description' => 'IV Cannula / Catheter G20 with injection port',
                'category' => ItemCategory::Supply,
                'unit_of_measure' => 'box of 50',
                'consignor_code' => 'BBRAUN',
                'minimum_level' => 15,
            ],
            [
                'code' => 'SUP-005',
                'description' => 'IV Cannula / Catheter G22 with injection port',
                'category' => ItemCategory::Supply,
                'unit_of_measure' => 'box of 50',
                'consignor_code' => 'BBRAUN',
                'minimum_level' => 20,
            ],
            [
                'code' => 'SUP-006',
                'description' => 'IV Infusion Set Adult (Standard)',
                'category' => ItemCategory::Supply,
                'unit_of_measure' => 'piece',
                'consignor_code' => 'BBRAUN',
                'minimum_level' => 100,
            ],
            [
                'code' => 'SUP-007',
                'description' => 'IV Infusion Set Pediatric (Micro-drip)',
                'category' => ItemCategory::Supply,
                'unit_of_measure' => 'piece',
                'consignor_code' => 'BBRAUN',
                'minimum_level' => 50,
            ],
            [
                'code' => 'SUP-008',
                'description' => 'Surgical Gauze Sponge 4x4 8-ply 100s',
                'category' => ItemCategory::Supply,
                'unit_of_measure' => 'pack',
                'consignor_code' => 'BEROVAN',
                'minimum_level' => 40,
            ],
            [
                'code' => 'SUP-009',
                'description' => 'Micropore Surgical Paper Tape 1 inch x 10 yards',
                'category' => ItemCategory::Supply,
                'unit_of_measure' => 'roll',
                'consignor_code' => 'BEROVAN',
                'minimum_level' => 30,
            ],
            [
                'code' => 'SUP-010',
                'description' => 'Latex Examination Gloves Powder-Free Medium 100s',
                'category' => ItemCategory::Supply,
                'unit_of_measure' => 'box',
                'consignor_code' => 'GLOBAN',
                'minimum_level' => 50,
            ],
            [
                'code' => 'SUP-011',
                'description' => 'Latex Examination Gloves Powder-Free Large 100s',
                'category' => ItemCategory::Supply,
                'unit_of_measure' => 'box',
                'consignor_code' => 'GLOBAN',
                'minimum_level' => 40,
            ],
            [
                'code' => 'SUP-012',
                'description' => 'Foley Bag Catheter 2-Way Fr 16 Silicone-Coated',
                'category' => ItemCategory::Supply,
                'unit_of_measure' => 'piece',
                'consignor_code' => 'HEALTHSOL',
                'minimum_level' => 25,
            ],
            [
                'code' => 'SUP-013',
                'description' => 'Urine Drainage Bag with anti-reflux valve 2000ml',
                'category' => ItemCategory::Supply,
                'unit_of_measure' => 'piece',
                'consignor_code' => 'HEALTHSOL',
                'minimum_level' => 30,
            ],
            [
                'code' => 'SUP-014',
                'description' => 'High-Flux Dialyzer Polysulfone F8HPS',
                'category' => ItemCategory::Supply,
                'unit_of_measure' => 'piece',
                'consignor_code' => 'FRESENIUS',
                'minimum_level' => 20,
            ],
            [
                'code' => 'SUP-015',
                'description' => 'Blood Tubing Lines Set for Hemodialysis',
                'category' => ItemCategory::Supply,
                'unit_of_measure' => 'set',
                'consignor_code' => 'FRESENIUS',
                'minimum_level' => 20,
            ],
            [
                'code' => 'SUP-016',
                'description' => 'AV Fistula Needle Arterial/Venous G16',
                'category' => ItemCategory::Supply,
                'unit_of_measure' => 'piece',
                'consignor_code' => 'FRESENIUS',
                'minimum_level' => 50,
            ],

            // Drugs & Medicines
            [
                'code' => 'DRG-001',
                'description' => 'Paracetamol 500mg Tablet',
                'category' => ItemCategory::Drug,
                'unit_of_measure' => 'box of 100',
                'consignor_code' => 'UNILAB',
                'minimum_level' => 50,
            ],
            [
                'code' => 'DRG-002',
                'description' => 'Paracetamol 10mg/ml IV Infusion 100ml',
                'category' => ItemCategory::Drug,
                'unit_of_measure' => 'vial',
                'consignor_code' => 'UNILAB',
                'minimum_level' => 40,
            ],
            [
                'code' => 'DRG-003',
                'description' => 'Cefuroxime Axetil 750mg Powder for Injection',
                'category' => ItemCategory::Drug,
                'unit_of_measure' => 'vial',
                'consignor_code' => 'PHARMAWEALTH',
                'minimum_level' => 30,
            ],
            [
                'code' => 'DRG-004',
                'description' => 'Ceftriaxone Disodium 1g Powder for Injection',
                'category' => ItemCategory::Drug,
                'unit_of_measure' => 'vial',
                'consignor_code' => 'PHARMAWEALTH',
                'minimum_level' => 40,
            ],
            [
                'code' => 'DRG-005',
                'description' => 'Omeprazole 40mg IV Powder for Injection',
                'category' => ItemCategory::Drug,
                'unit_of_measure' => 'vial',
                'consignor_code' => 'ENDURE',
                'minimum_level' => 35,
            ],
            [
                'code' => 'DRG-006',
                'description' => 'Tramadol Hydrochloride 50mg/ml 2ml Ampule',
                'category' => ItemCategory::Drug,
                'unit_of_measure' => 'ampule',
                'consignor_code' => 'METRODRUG',
                'minimum_level' => 25,
            ],
            [
                'code' => 'DRG-007',
                'description' => 'Ketorolac Tromethamine 30mg/ml 1ml Ampule',
                'category' => ItemCategory::Drug,
                'unit_of_measure' => 'ampule',
                'consignor_code' => 'METRODRUG',
                'minimum_level' => 20,
            ],
            [
                'code' => 'DRG-008',
                'description' => '0.9% Sodium Chloride (Normal Saline) 1 Liter',
                'category' => ItemCategory::Drug,
                'unit_of_measure' => 'bottle',
                'consignor_code' => 'BBRAUN',
                'minimum_level' => 100,
            ],
            [
                'code' => 'DRG-009',
                'description' => 'Plain Lactated Ringer\'s Solution 1 Liter',
                'category' => ItemCategory::Drug,
                'unit_of_measure' => 'bottle',
                'consignor_code' => 'BBRAUN',
                'minimum_level' => 80,
            ],
            [
                'code' => 'DRG-010',
                'description' => '5% Dextrose in Water (D5W) 1 Liter',
                'category' => ItemCategory::Drug,
                'unit_of_measure' => 'bottle',
                'consignor_code' => 'BBRAUN',
                'minimum_level' => 60,
            ],
            [
                'code' => 'DRG-011',
                'description' => 'Furosemide 10mg/ml 2ml Ampule',
                'category' => ItemCategory::Drug,
                'unit_of_measure' => 'ampule',
                'consignor_code' => 'ZUELLIG',
                'minimum_level' => 30,
            ],
            [
                'code' => 'DRG-012',
                'description' => 'Potassium Chloride 2mEq/ml 20ml Vial',
                'category' => ItemCategory::Drug,
                'unit_of_measure' => 'vial',
                'consignor_code' => 'ZUELLIG',
                'minimum_level' => 25,
            ],
            [
                'code' => 'DRG-013',
                'description' => 'Heparin Sodium 1000 IU/ml 5ml Vial',
                'category' => ItemCategory::Drug,
                'unit_of_measure' => 'vial',
                'consignor_code' => 'FRESENIUS',
                'minimum_level' => 20,
            ],
            [
                'code' => 'DRG-014',
                'description' => 'Epoetin Alfa (EPO) 4000 IU Pre-filled Syringe',
                'category' => ItemCategory::Drug,
                'unit_of_measure' => 'piece',
                'consignor_code' => 'FRESENIUS',
                'minimum_level' => 15,
            ],
            [
                'code' => 'DRG-015',
                'description' => 'Metformin Hydrochloride 500mg Film-Coated Tablet',
                'category' => ItemCategory::Drug,
                'unit_of_measure' => 'box of 100',
                'consignor_code' => 'UNILAB',
                'minimum_level' => 30,
            ],
            [
                'code' => 'DRG-016',
                'description' => 'Amlodipine Besilate 5mg Tablet',
                'category' => ItemCategory::Drug,
                'unit_of_measure' => 'box of 100',
                'consignor_code' => 'UNILAB',
                'minimum_level' => 30,
            ],
        ];

        foreach ($items as $item) {
            $consignorId = $consignors[$item['consignor_code']] ?? $consignors->first();

            Item::updateOrCreate(
                ['code' => $item['code']],
                [
                    'description' => $item['description'],
                    'category' => $item['category'],
                    'unit_of_measure' => $item['unit_of_measure'],
                    'consignor_id' => $consignorId,
                    'minimum_level' => $item['minimum_level'],
                    'is_active' => true,
                ]
            );
        }
    }
}
