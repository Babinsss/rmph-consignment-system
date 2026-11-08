<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
            [
                'name' => 'Central Supply Room (CSR)',
                'code' => 'CSR',
                'description' => 'Central Supply Room ward unit',
            ],
            [
                'name' => 'High Dependency Unit (HDU)',
                'code' => 'HDU',
                'description' => 'High Dependency Unit ward',
            ],
            [
                'name' => 'Gugma Dialysis Center',
                'code' => 'DIALYSIS',
                'description' => 'Gugma Dialysis Center clinic',
            ],
            [
                'name' => 'Pharmacy Main',
                'code' => 'PHARM_MAIN',
                'description' => 'Main Inpatient Hospital Pharmacy',
            ],
            [
                'name' => 'Pharmacy OPD',
                'code' => 'PHARM_OPD',
                'description' => 'Outpatient Department Pharmacy',
            ],
            [
                'name' => 'Yakap',
                'code' => 'YAKAP',
                'description' => 'Yakap Ward / Clinic',
            ],
        ];

        foreach ($units as $unit) {
            Unit::updateOrCreate(
                ['code' => $unit['code']],
                [
                    'name' => $unit['name'],
                    'description' => $unit['description'],
                    'is_active' => true,
                ]
            );
        }
    }
}
