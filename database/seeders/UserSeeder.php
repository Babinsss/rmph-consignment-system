<?php

namespace Database\Seeders;

use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $password = Hash::make('password');

        // 1. Administrator (RMPH IT)
        $admin = User::updateOrCreate(
            ['email' => 'admin@rmph.local'],
            [
                'name' => 'RMPH IT Administrator',
                'password' => $password,
                'unit_id' => null,
                'is_active' => true,
            ]
        );
        $admin->syncRoles(['admin']);

        // 2. Custodians
        $custodianSupplies = User::updateOrCreate(
            ['email' => 'aliana.dula@rmph.local'],
            [
                'name' => 'Aliana Marie Dula (Supplies Custodian)',
                'password' => $password,
                'unit_id' => null,
                'is_active' => true,
            ]
        );
        $custodianSupplies->syncRoles(['custodian_supplies']);

        $custodianDrugs = User::updateOrCreate(
            ['email' => 'kristine.batan@rmph.local'],
            [
                'name' => 'Kristine Mae Batan (Drugs Custodian)',
                'password' => $password,
                'unit_id' => null,
                'is_active' => true,
            ]
        );
        $custodianDrugs->syncRoles(['custodian_drugs']);

        // 3. Viewer (Chief of Hospital)
        $chief = User::updateOrCreate(
            ['email' => 'chief@rmph.local'],
            [
                'name' => 'Chief of Hospital',
                'password' => $password,
                'unit_id' => null,
                'is_active' => true,
            ]
        );
        $chief->syncRoles(['viewer']);

        // 4. Requesting Unit Staff (for each of the 6 units)
        $units = Unit::all();
        $unitAccounts = [
            'CSR' => ['email' => 'csr@rmph.local', 'name' => 'CSR Ward Staff'],
            'HDU' => ['email' => 'hdu@rmph.local', 'name' => 'HDU Ward Staff'],
            'DIALYSIS' => ['email' => 'dialysis@rmph.local', 'name' => 'Gugma Dialysis Staff'],
            'PHARM_MAIN' => ['email' => 'pharmacy.main@rmph.local', 'name' => 'Pharmacy Main Staff'],
            'PHARM_OPD' => ['email' => 'pharmacy.opd@rmph.local', 'name' => 'Pharmacy OPD Staff'],
            'YAKAP' => ['email' => 'yakap@rmph.local', 'name' => 'Yakap Clinic Staff'],
        ];

        foreach ($units as $unit) {
            $accountData = $unitAccounts[$unit->code] ?? [
                'email' => strtolower($unit->code).'@rmph.local',
                'name' => $unit->name.' Staff',
            ];

            $unitUser = User::updateOrCreate(
                ['email' => $accountData['email']],
                [
                    'name' => $accountData['name'],
                    'password' => $password,
                    'unit_id' => $unit->id,
                    'is_active' => true,
                ]
            );
            $unitUser->syncRoles(['requesting_unit']);
        }
    }
}
