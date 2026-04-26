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
            $permission = Permission::firstOrCreate([
                'name' => 'view-audit-log',
                'guard_name' => 'customer_user',
            ]);

            foreach (['Owner', 'Admin'] as $roleName) {
                $role = Role::where(['name' => $roleName, 'guard_name' => 'customer_user'])->first();
                $role?->givePermissionTo($permission);
            }
        });

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        Permission::where(['name' => 'view-audit-log', 'guard_name' => 'customer_user'])->delete();
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
