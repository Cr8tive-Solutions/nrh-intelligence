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
            // Note: view-audit-log is granted to Admin too (kept in the diff above by inclusion)
            'Member' => [
                'view-dashboard',
                'view-candidates',
                'view-requests',
                'create-requests',
                'view-reports',
            ],
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
