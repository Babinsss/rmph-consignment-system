<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Define system permissions
        $permissions = [
            'manage_users',
            'manage_catalog',
            'record_deliveries',
            'view_unit_costs',
            'fulfill_requests',
            'view_inventory',
            'submit_requests',
            'view_own_requests',
            'view_all_requests',
            'view_reports',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        // 1. Administrator (RMPH IT)
        $adminRole = Role::findOrCreate('admin', 'web');
        $adminRole->syncPermissions(Permission::all());

        // 2a. Generic Custodian (legacy / admin use — full access to all categories)
        $custodianRole = Role::findOrCreate('custodian', 'web');
        $custodianRole->syncPermissions([
            'manage_catalog',
            'record_deliveries',
            'view_unit_costs',
            'fulfill_requests',
            'view_inventory',
            'view_all_requests',
            'view_reports',
        ]);

        // 2b. Custodian – Medical Supplies only (Aliana Marie Dula)
        $custodianSuppliesRole = Role::findOrCreate('custodian_supplies', 'web');
        $custodianSuppliesRole->syncPermissions([
            'manage_catalog',
            'record_deliveries',
            'view_unit_costs',
            'fulfill_requests',
            'view_inventory',
            'view_all_requests',
            'view_reports',
        ]);

        // 2c. Custodian – Drugs & Medicine only (Kristine Mae Batan)
        $custodianDrugsRole = Role::findOrCreate('custodian_drugs', 'web');
        $custodianDrugsRole->syncPermissions([
            'manage_catalog',
            'record_deliveries',
            'view_unit_costs',
            'fulfill_requests',
            'view_inventory',
            'view_all_requests',
            'view_reports',
        ]);

        // 3. Viewer (Chief of Hospital - Read-only monitor)
        $viewerRole = Role::findOrCreate('viewer', 'web');
        $viewerRole->syncPermissions([
            'view_inventory',
            'view_unit_costs',
            'view_all_requests',
            'view_reports',
        ]);

        // 4. Requesting Unit Staff (CSR, HDU, Gugma Dialysis, Pharmacy Main, Pharmacy OPD, Yakap)
        // Strictly CANNOT see unit cost
        $requestingUnitRole = Role::findOrCreate('requesting_unit', 'web');
        $requestingUnitRole->syncPermissions([
            'submit_requests',
            'view_own_requests',
            'view_inventory',
        ]);
    }
}
