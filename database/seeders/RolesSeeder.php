<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        $allPermissions = Permission::where('guard_name', 'customer_user')->pluck('name')->all();

        $roles = [
            'Owner' => $allPermissions,
            'Admin' => array_values(array_diff($allPermissions, ['manage-billing', 'manage-team'])),
            // No 'view-billing': HR orders checks and reads reports; money is the
            // Accounts role's job. See the remove_view_billing_from_hr_role migration.
            'HR' => [
                'view-dashboard',
                'view-candidates',
                'view-requests',
                'create-requests',
                'view-reports',
            ],
            'Accounts' => [
                'view-dashboard',
                'view-candidates',
                'view-requests',
                'view-reports',
                'view-billing',
                'manage-billing',
                'view-prices',
                'download-invoices',
                'view-audit-log',
            ],
            // No 'Member' role — retired in favour of HR, which it only differed
            // from by lacking view-billing. See the remove_member_role migration.
            'Viewer' => [
                'view-dashboard',
                'view-candidates',
                'view-requests',
                'view-reports',
            ],
        ];

        DB::transaction(function () use ($roles) {
            foreach ($roles as $name => $permissions) {
                $role = Role::firstOrCreate([
                    'name' => $name,
                    'guard_name' => 'customer_user',
                ]);
                $role->syncPermissions($permissions);
            }
        });

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
