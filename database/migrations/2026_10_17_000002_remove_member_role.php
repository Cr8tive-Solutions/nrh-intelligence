<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

/**
 * Retire the `Member` role.
 *
 * Member differed from HR only by lacking `view-billing` — both could create
 * requests and neither could see prices. Two near-identical roles is just a way
 * to misconfigure access, so Member users move to HR.
 *
 * HR is the safe target: it keeps `create-requests`, so nobody loses the ability
 * to do their job. The only widening is `view-billing`, which shows the invoice
 * *list* — amounts stay behind `view-prices`, which HR does not have.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function () {
            $member = DB::table('roles')
                ->where(['name' => 'Member', 'guard_name' => 'customer_user'])
                ->first();

            if (! $member) {
                return;
            }

            $hr = DB::table('roles')
                ->where(['name' => 'HR', 'guard_name' => 'customer_user'])
                ->first();

            // Without HR there is nothing safe to migrate to — leave Member alone
            // rather than stranding its users with no role at all.
            if (! $hr) {
                return;
            }

            foreach (DB::table('model_has_roles')->where('role_id', $member->id)->get() as $assignment) {
                $alreadyHasHr = DB::table('model_has_roles')
                    ->where('role_id', $hr->id)
                    ->where('model_id', $assignment->model_id)
                    ->where('model_type', $assignment->model_type)
                    ->exists();

                if (! $alreadyHasHr) {
                    DB::table('model_has_roles')->insert([
                        'role_id' => $hr->id,
                        'model_id' => $assignment->model_id,
                        'model_type' => $assignment->model_type,
                    ]);
                }
            }

            DB::table('model_has_roles')->where('role_id', $member->id)->delete();
            DB::table('role_has_permissions')->where('role_id', $member->id)->delete();
            DB::table('roles')->where('id', $member->id)->delete();
        });

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * Recreate Member with its original permission set. Users are not moved
     * back — HR is a superset, so leaving them there loses nothing.
     */
    public function down(): void
    {
        DB::transaction(function () {
            $exists = DB::table('roles')
                ->where(['name' => 'Member', 'guard_name' => 'customer_user'])
                ->exists();

            if ($exists) {
                return;
            }

            $roleId = DB::table('roles')->insertGetId([
                'name' => 'Member',
                'guard_name' => 'customer_user',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $permissionIds = DB::table('permissions')
                ->where('guard_name', 'customer_user')
                ->whereIn('name', [
                    'view-dashboard',
                    'view-candidates',
                    'view-requests',
                    'create-requests',
                    'view-reports',
                ])
                ->pluck('id');

            foreach ($permissionIds as $permissionId) {
                DB::table('role_has_permissions')->insert([
                    'role_id' => $roleId,
                    'permission_id' => $permissionId,
                ]);
            }
        });

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
