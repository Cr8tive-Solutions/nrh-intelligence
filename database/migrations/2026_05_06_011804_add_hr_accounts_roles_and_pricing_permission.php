<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function () {
            // New fine-grained permissions
            $newPermissions = ['view-prices', 'download-invoices'];
            foreach ($newPermissions as $name) {
                Permission::firstOrCreate(['name' => $name, 'guard_name' => 'customer_user']);
            }

            $allPerms = Permission::where('guard_name', 'customer_user')->pluck('id', 'name')->all();

            // Owner gets everything (refresh).
            $owner = Role::where(['name' => 'Owner', 'guard_name' => 'customer_user'])->first();
            $owner?->syncPermissions(array_keys($allPerms));

            // Admin: everything except billing+team management. Includes new view-prices + download-invoices.
            $admin = Role::where(['name' => 'Admin', 'guard_name' => 'customer_user'])->first();
            if ($admin) {
                $adminPerms = array_diff(array_keys($allPerms), ['manage-billing', 'manage-team']);
                $admin->syncPermissions($adminPerms);
            }

            // HR — submits orders, sees scopes (no prices), sees invoice list (no download/details), no team/billing mgmt.
            $hr = Role::firstOrCreate(['name' => 'HR', 'guard_name' => 'customer_user']);
            $hr->syncPermissions([
                'view-dashboard',
                'view-candidates',
                'view-requests',
                'create-requests',
                'view-reports',
                'view-billing',
            ]);

            // Accounts — full visibility on reports/billing/prices, downloads invoices, but doesn't create orders.
            $accounts = Role::firstOrCreate(['name' => 'Accounts', 'guard_name' => 'customer_user']);
            $accounts->syncPermissions([
                'view-dashboard',
                'view-candidates',
                'view-requests',
                'view-reports',
                'view-billing',
                'manage-billing',
                'view-prices',
                'download-invoices',
                'view-audit-log',
            ]);
        });

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        DB::transaction(function () {
            Role::where(['name' => 'HR', 'guard_name' => 'customer_user'])->delete();
            Role::where(['name' => 'Accounts', 'guard_name' => 'customer_user'])->delete();
            Permission::whereIn('name', ['view-prices', 'download-invoices'])
                ->where('guard_name', 'customer_user')
                ->delete();
        });

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
