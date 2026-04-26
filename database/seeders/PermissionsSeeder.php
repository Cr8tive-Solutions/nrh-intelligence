<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'view-dashboard',
            'view-candidates',
            'manage-candidates',
            'view-requests',
            'create-requests',
            'view-billing',
            'manage-billing',
            'view-reports',
            'manage-team',
            'manage-packages',
            'manage-settings',
            'view-audit-log',
        ];

        DB::transaction(function () use ($permissions) {
            foreach ($permissions as $name) {
                Permission::firstOrCreate([
                    'name' => $name,
                    'guard_name' => 'customer_user',
                ]);
            }
        });

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
