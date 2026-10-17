<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

/**
 * Take `view-billing` away from HR.
 *
 * HR submits candidates and reads reports; money is the Accounts role's job —
 * that separation is the reason `view-prices` exists. Billing bought HR a nav
 * section of dead ends: it cannot open or download a single invoice or receipt
 * (`download-invoices` blocks that), and the one money fact HR actually needs —
 * a cash-billed request being held pending payment — shows on the request page.
 *
 * Only HR is touched. Owner/Admin/Accounts keep `view-billing`, and the
 * permission itself is left in place.
 */
return new class extends Migration
{
    public function up(): void
    {
        $this->setHrBillingAccess(granted: false);
    }

    public function down(): void
    {
        $this->setHrBillingAccess(granted: true);
    }

    private function setHrBillingAccess(bool $granted): void
    {
        DB::transaction(function () use ($granted) {
            $hr = DB::table('roles')
                ->where(['name' => 'HR', 'guard_name' => 'customer_user'])
                ->first();

            $permission = DB::table('permissions')
                ->where(['name' => 'view-billing', 'guard_name' => 'customer_user'])
                ->first();

            if (! $hr || ! $permission) {
                return;
            }

            $link = DB::table('role_has_permissions')
                ->where('role_id', $hr->id)
                ->where('permission_id', $permission->id);

            if ($granted) {
                if (! $link->exists()) {
                    DB::table('role_has_permissions')->insert([
                        'role_id' => $hr->id,
                        'permission_id' => $permission->id,
                    ]);
                }

                return;
            }

            $link->delete();
        });

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
