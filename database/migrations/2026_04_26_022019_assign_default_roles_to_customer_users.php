<?php

use App\Models\CustomerUser;
use Database\Seeders\PermissionsSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Artisan::call('db:seed', ['--class' => PermissionsSeeder::class, '--force' => true]);
        Artisan::call('db:seed', ['--class' => RolesSeeder::class, '--force' => true]);

        DB::transaction(function () {
            $customerIds = CustomerUser::query()->distinct()->pluck('customer_id');

            foreach ($customerIds as $customerId) {
                $oldestAdminId = CustomerUser::where('customer_id', $customerId)
                    ->where('role', 'admin')
                    ->orderBy('created_at')
                    ->orderBy('id')
                    ->value('id');

                CustomerUser::where('customer_id', $customerId)
                    ->orderBy('id')
                    ->get()
                    ->each(function (CustomerUser $user) use ($oldestAdminId) {
                        $roleName = match (true) {
                            $user->id === $oldestAdminId => 'Owner',
                            $user->role === 'admin' => 'Admin',
                            default => 'Member',
                        };
                        $user->syncRoles([$roleName]);
                    });
            }
        });
    }

    public function down(): void
    {
        DB::table('model_has_roles')
            ->where('model_type', CustomerUser::class)
            ->delete();
    }
};
