<?php

use App\Models\CustomerRegistration;
use App\Models\CustomerUser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\Support\Fixtures;

/**
 * Invite-to-register onboarding, invitee side. Staff create the invitation in
 * the admin portal; here the invitee submits their details for review. Nothing
 * can sign in until staff approve it over there.
 */
beforeEach(function () {
    Fixtures::country(['name' => 'Malaysia']);

    $this->adminId = DB::table('admins')->insertGetId([
        'name' => 'NRH Staff',
        'email' => Fixtures::uniq('staff').'@example.test',
        'password' => bcrypt('x'),
        'role' => 'super_admin',
        'status' => 'active',
        'created_at' => now(), 'updated_at' => now(),
    ]);

    $this->token = bin2hex(random_bytes(32));
    $this->email = Fixtures::uniq('prospect').'@example.test';
    $this->registrationId = DB::table('customer_registrations')->insertGetId([
        'email' => $this->email,
        'status' => 'invited',
        'token_hash' => hash('sha256', $this->token),
        'expires_at' => now()->addDays(14),
        'invited_by_admin_id' => $this->adminId,
        'company_name' => 'Prefilled Sdn Bhd',
        'created_at' => now(), 'updated_at' => now(),
    ]);
});

function registrationForm(array $overrides = []): array
{
    return array_merge([
        'company_name' => 'Maju Jaya Sdn Bhd',
        'registration_no' => '202301012345',
        'address' => '12 Jalan Ampang, Kuala Lumpur',
        'country' => 'Malaysia',
        'industry' => 'Logistics',
        'contact_name' => 'Aminah Yusof',
        'contact_designation' => 'HR Manager',
        'contact_phone' => '+60123456789',
        'password' => 'chosen-password',
        'password_confirmation' => 'chosen-password',
        'declaration' => '1',
    ], $overrides);
}

it('shows the registration form for a valid link, prefilled by staff', function () {
    $this->get(route('client.registration.show', $this->token))
        ->assertOk()
        ->assertSee('Prefilled Sdn Bhd')
        ->assertSee($this->email)
        ->assertSee('Submit for approval');
});

it('records the submission for review without creating a login', function () {
    $this->post(route('client.registration.submit', $this->token), registrationForm())
        ->assertRedirect(route('client.registration.show', $this->token));

    $r = CustomerRegistration::findOrFail($this->registrationId);

    expect($r->status)->toBe('submitted')
        ->and($r->company_name)->toBe('Maju Jaya Sdn Bhd')
        ->and($r->contact_phone)->toBe('+60123456789')
        ->and($r->submitted_at)->not->toBeNull()
        ->and(Hash::check('chosen-password', $r->password_hash))->toBeTrue()
        // No account until staff approve.
        ->and(CustomerUser::where('email', $this->email)->exists())->toBeFalse();

    // The inviting staff member is notified.
    expect(DB::table('admin_notifications')
        ->where('admin_id', $this->adminId)
        ->where('reference', 'registration:'.$this->registrationId)
        ->exists())->toBeTrue();

    // Re-opening the link shows the "received" state, and it can't be resubmitted.
    $this->get(route('client.registration.show', $this->token))
        ->assertOk()
        ->assertSee('received', false)
        ->assertDontSee('Submit for approval');

    $this->post(route('client.registration.submit', $this->token), registrationForm(['company_name' => 'Changed']));
    expect($r->fresh()->company_name)->toBe('Maju Jaya Sdn Bhd');
});

it('validates required details and the declaration', function () {
    $this->post(route('client.registration.submit', $this->token), registrationForm([
        'registration_no' => '',
        'password_confirmation' => 'different',
        'declaration' => null,
    ]))->assertSessionHasErrors(['registration_no', 'password', 'declaration']);

    expect(CustomerRegistration::findOrFail($this->registrationId)->status)->toBe('invited');
});

it('rejects unknown and expired links', function () {
    $this->get(route('client.registration.show', 'not-a-real-token'))
        ->assertOk()
        ->assertSee('unavailable', false)
        ->assertDontSee('Submit for approval');

    DB::table('customer_registrations')->where('id', $this->registrationId)->update(['expires_at' => now()->subMinute()]);

    $this->get(route('client.registration.show', $this->token))->assertSee('expired', false);
    $this->post(route('client.registration.submit', $this->token), registrationForm());

    expect(CustomerRegistration::findOrFail($this->registrationId)->status)->toBe('invited');
});

it('refuses to submit when the email already has a portal login', function () {
    Fixtures::seedRoles();
    Fixtures::user(Fixtures::customer(), 'Owner', ['email' => $this->email]);

    $this->post(route('client.registration.submit', $this->token), registrationForm())
        ->assertSessionHasErrors('email');

    expect(CustomerRegistration::findOrFail($this->registrationId)->status)->toBe('invited');
});
