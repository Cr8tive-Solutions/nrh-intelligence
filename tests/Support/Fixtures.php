<?php

namespace Tests\Support;

use App\Models\CustomerUser;
use Database\Seeders\PermissionsSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

/**
 * Row builders for the shared schema (client-portal side).
 *
 * The test database is a structure-only snapshot of production, so every test
 * starts from an empty schema and builds exactly the rows it needs.
 */
class Fixtures
{
    private static int $seq = 0;

    public static function uniq(string $prefix = ''): string
    {
        return $prefix.(++self::$seq).'-'.substr(bin2hex(random_bytes(4)), 0, 6);
    }

    /** Seed spatie roles + permissions for the customer_user guard. */
    public static function seedRoles(): void
    {
        if (Permission::where('guard_name', 'customer_user')->count() === 0) {
            (new PermissionsSeeder)->run();
            (new RolesSeeder)->run();
        }
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public static function country(array $attrs = []): int
    {
        return DB::table('countries')->insertGetId(array_merge([
            'name' => 'Malaysia',
            'code' => strtoupper(substr(self::uniq(), 0, 3)),
            'created_at' => now(), 'updated_at' => now(),
        ], $attrs));
    }

    public static function identityType(array $attrs = []): int
    {
        return DB::table('identity_types')->insertGetId(array_merge([
            'name' => 'MyKad',
            'created_at' => now(), 'updated_at' => now(),
        ], $attrs));
    }

    public static function customer(array $attrs = []): int
    {
        return DB::table('customers')->insertGetId(array_merge([
            'name' => 'Acme Sdn Bhd '.self::uniq(),
            'contact_name' => 'Contact Person',
            'contact_email' => self::uniq('contact').'@example.test',
            'created_at' => now(), 'updated_at' => now(),
        ], $attrs));
    }

    /** Create a customer user and give it a role on the customer_user guard. */
    public static function user(int $customerId, string $role = 'Owner', array $attrs = []): CustomerUser
    {
        $user = CustomerUser::create(array_merge([
            'customer_id' => $customerId,
            'name' => 'Client User',
            'email' => self::uniq('user').'@example.test',
            'password' => bcrypt('secret-password'),
            'role' => 'admin',
            'status' => 'active',
        ], $attrs));

        self::seedRoles();
        $user->assignRole($role);

        return $user;
    }

    public static function agreement(int $customerId, string $billing = 'monthly', array $attrs = []): int
    {
        return DB::table('agreements')->insertGetId(array_merge([
            'customer_id' => $customerId,
            'type' => 'standard',
            'start_date' => now()->subYear()->toDateString(),
            'expiry_date' => now()->addYear()->toDateString(),
            'sla_tat' => '5 days',
            'billing' => $billing,
            'payment' => 'bank_transfer',
            'created_at' => now(), 'updated_at' => now(),
        ], $attrs));
    }

    public static function scopeType(int $countryId, array $attrs = []): int
    {
        return DB::table('scope_types')->insertGetId(array_merge([
            'country_id' => $countryId,
            'name' => 'Crime Risk Integrity '.self::uniq(),
            'turnaround' => '3 days',
            'price' => 100.00,
            'price_on_request' => false,
            'created_at' => now(), 'updated_at' => now(),
        ], $attrs));
    }

    public static function screeningRequest(int $customerId, int $customerUserId, array $attrs = []): int
    {
        return DB::table('screening_requests')->insertGetId(array_merge([
            'customer_id' => $customerId,
            'customer_user_id' => $customerUserId,
            'reference' => 'REQ-'.now()->format('Y').'-'.self::uniq(),
            'status' => 'in_progress',
            'type' => 'employment_global',
            'created_at' => now(), 'updated_at' => now(),
        ], $attrs));
    }

    public static function candidate(int $requestId, int $identityTypeId, array $attrs = []): int
    {
        return DB::table('request_candidates')->insertGetId(array_merge([
            'screening_request_id' => $requestId,
            'identity_type_id' => $identityTypeId,
            'name' => 'Candidate Name',
            'identity_number' => '900101-01-'.random_int(1000, 9999),
            'status' => 'new',
            'created_at' => now(), 'updated_at' => now(),
        ], $attrs));
    }

    public static function invoice(int $customerId, array $attrs = []): int
    {
        return DB::table('invoices')->insertGetId(array_merge([
            'customer_id' => $customerId,
            'number' => 'INV-'.now()->format('Y').'-'.self::uniq(),
            'period' => now()->format('F Y'),
            'status' => 'unpaid',
            'issued_at' => now()->toDateString(),
            'due_at' => now()->addDays(30)->toDateString(),
            'subtotal' => 100.00,
            'tax' => 6.00,
            'total' => 106.00,
            'created_at' => now(), 'updated_at' => now(),
        ], $attrs));
    }
}
