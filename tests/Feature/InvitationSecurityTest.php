<?php

use App\Models\CustomerUser;
use App\Models\CustomerUserInvitation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\Support\Fixtures;

/**
 * Team invitations and team-management hardening:
 *  - M12: invitation tokens are stored hashed; links carry the raw token.
 *  - M7:  invitations only ever activate never-activated accounts.
 *  - M6:  managers may change the email only of a pending invitee.
 *  - M5:  a role can only be assigned by someone holding all its permissions.
 */
beforeEach(function () {
    Fixtures::seedRoles();
    $this->customerId = Fixtures::customer();
    $this->owner = Fixtures::user($this->customerId, 'Owner');
});

/** An invitation row for the user; returns the raw token the link would carry. */
function inviteRow(CustomerUser $user, array $attrs = []): string
{
    $rawToken = bin2hex(random_bytes(32));

    DB::table('customer_user_invitations')->insert(array_merge([
        'customer_user_id' => $user->id,
        'token' => hash('sha256', $rawToken),
        'expires_at' => now()->addDays(14),
        'sent_count' => 1,
        'last_sent_at' => now()->subHour(),
        'created_at' => now(), 'updated_at' => now(),
    ], $attrs));

    return $rawToken;
}

function pendingInvitee(int $customerId, string $role = 'HR'): CustomerUser
{
    return Fixtures::user($customerId, $role, ['status' => 'inactive']);
}

/** The raw token from the most recently emailed invitation link. */
function lastEmailedInvitationToken(): string
{
    $messages = Mail::getSymfonyTransport()->messages();
    $body = $messages[count($messages) - 1]->getOriginalMessage()->getTextBody();

    preg_match('#/invitation/([A-Za-z0-9]+)#', $body, $match);

    return $match[1];
}

// ── M12 — hashed tokens ──────────────────────────────────────────────────────

it('stores only the hash of a new invitation token and emails the raw one', function () {
    $this->actingAs($this->owner, 'customer_user')
        ->post(route('client.settings.users.store'), [
            'name' => 'New Hire',
            'email' => 'new-hire@example.test',
            'role' => 'HR',
        ])
        ->assertRedirect(route('client.settings.users'));

    $user = CustomerUser::where('email', 'new-hire@example.test')->firstOrFail();
    $stored = CustomerUserInvitation::where('customer_user_id', $user->id)->value('token');
    $rawToken = lastEmailedInvitationToken();

    expect($stored)->toMatch('/^[0-9a-f]{64}$/')
        ->and($stored)->toBe(hash('sha256', $rawToken))
        ->and($stored)->not->toBe($rawToken);
});

it('opens and accepts an invitation by its raw token', function () {
    $invitee = pendingInvitee($this->customerId);
    $rawToken = inviteRow($invitee);

    $this->get(route('client.invitation.show', $rawToken))->assertOk();

    $this->post(route('client.invitation.accept', $rawToken), [
        'password' => 'Brand-New-Passw0rd',
        'password_confirmation' => 'Brand-New-Passw0rd',
    ])->assertRedirect(route('client.dashboard'));

    expect($invitee->fresh()->status)->toBe('active');
});

it('does not accept the stored hash itself as a link token', function () {
    $invitee = pendingInvitee($this->customerId);
    $rawToken = inviteRow($invitee);

    $this->get(route('client.invitation.show', hash('sha256', $rawToken)))
        ->assertOk()
        ->assertViewIs('client.invitation.invalid');
});

it('rejects a weak password when accepting an invitation', function () {
    $invitee = pendingInvitee($this->customerId);
    $rawToken = inviteRow($invitee);

    $this->post(route('client.invitation.accept', $rawToken), [
        'password' => 'weakpassword',
        'password_confirmation' => 'weakpassword',
    ])->assertSessionHasErrors('password');

    expect($invitee->fresh()->status)->toBe('inactive');
});

// ── M7 — accept / resend only for never-activated users ──────────────────────

it('refuses an invitation for a user who was previously activated', function () {
    $former = Fixtures::user($this->customerId, 'HR', ['status' => 'inactive', 'password' => bcrypt('Old-Passw0rd-123')]);
    inviteRow($former, ['accepted_at' => now()->subMonth(), 'expires_at' => now()->subMonth()]);
    $leftoverToken = inviteRow($former);

    $this->post(route('client.invitation.accept', $leftoverToken), [
        'password' => 'Attacker-Passw0rd',
        'password_confirmation' => 'Attacker-Passw0rd',
    ])->assertRedirect(route('client.login'));

    $former->refresh();
    expect($former->status)->toBe('inactive')
        ->and(Hash::check('Old-Passw0rd-123', $former->password))->toBeTrue()
        ->and(auth('customer_user')->check())->toBeFalse();
});

it('resends a pending invitation with a fresh token', function () {
    $invitee = pendingInvitee($this->customerId);
    $oldToken = inviteRow($invitee);

    $this->actingAs($this->owner, 'customer_user')
        ->post(route('client.settings.users.resend-invitation', $invitee))
        ->assertSessionHas('status');

    $newToken = lastEmailedInvitationToken();

    expect($newToken)->not->toBe($oldToken)
        ->and(CustomerUserInvitation::where('customer_user_id', $invitee->id)->value('token'))
        ->toBe(hash('sha256', $newToken));
});

it('refuses to resend an invitation to a deactivated, previously active user', function () {
    $former = Fixtures::user($this->customerId, 'HR', ['status' => 'inactive']);
    inviteRow($former, ['accepted_at' => now()->subMonth()]);

    $this->actingAs($this->owner, 'customer_user')
        ->post(route('client.settings.users.resend-invitation', $former))
        ->assertSessionHas('error');

    expect(Mail::getSymfonyTransport()->messages())->toHaveCount(0);
});

it('refuses to resend an invitation to a user who outranks the manager', function () {
    $admin = Fixtures::user($this->customerId, 'Admin');
    $admin->givePermissionTo('manage-team');
    $pendingOwner = pendingInvitee($this->customerId, 'Owner');
    inviteRow($pendingOwner);

    $this->actingAs($admin, 'customer_user')
        ->post(route('client.settings.users.resend-invitation', $pendingOwner))
        ->assertForbidden();
});

// ── M6 — email changes from team management ──────────────────────────────────

/** The update payload for a user, with overrides. */
function userUpdatePayload(CustomerUser $user, array $overrides = []): array
{
    return array_merge([
        'name' => $user->name,
        'email' => $user->email,
        'role' => $user->roles->first()->name,
        'status' => $user->status,
    ], $overrides);
}

it('refuses to change the email of an active user', function () {
    $member = Fixtures::user($this->customerId, 'HR');
    $originalEmail = $member->email;

    $this->actingAs($this->owner, 'customer_user')
        ->put(route('client.settings.users.update', $member), userUpdatePayload($member, ['email' => 'takeover@example.test']))
        ->assertSessionHasErrors('email');

    expect($member->fresh()->email)->toBe($originalEmail);
});

it('refuses to change the email of a deactivated, previously active user', function () {
    $former = Fixtures::user($this->customerId, 'HR', ['status' => 'inactive']);
    inviteRow($former, ['accepted_at' => now()->subMonth()]);
    $originalEmail = $former->email;

    $this->actingAs($this->owner, 'customer_user')
        ->put(route('client.settings.users.update', $former), userUpdatePayload($former, ['email' => 'takeover@example.test']))
        ->assertSessionHasErrors('email');

    expect($former->fresh()->email)->toBe($originalEmail);
});

it('lets a manager correct a pending invitee email and retires the old link', function () {
    $invitee = pendingInvitee($this->customerId);
    $oldToken = inviteRow($invitee);

    $this->actingAs($this->owner, 'customer_user')
        ->put(route('client.settings.users.update', $invitee), userUpdatePayload($invitee, ['email' => 'corrected@example.test']))
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('client.settings.users'));

    expect($invitee->fresh()->email)->toBe('corrected@example.test');

    $this->post(route('client.invitation.accept', $oldToken), [
        'password' => 'Brand-New-Passw0rd',
        'password_confirmation' => 'Brand-New-Passw0rd',
    ])->assertRedirect(route('client.login'));

    expect($invitee->fresh()->status)->toBe('inactive');
});

it('still lets a manager rename an active user', function () {
    $member = Fixtures::user($this->customerId, 'HR');

    $this->actingAs($this->owner, 'customer_user')
        ->put(route('client.settings.users.update', $member), userUpdatePayload($member, ['name' => 'Renamed Person']))
        ->assertSessionHasNoErrors();

    expect($member->fresh()->name)->toBe('Renamed Person');
});

// ── M5 — role assignment needs the role's permissions ────────────────────────

it('refuses to assign a role carrying permissions the manager lacks', function () {
    // Admin outranks Accounts but does not hold manage-billing, which Accounts has.
    $admin = Fixtures::user($this->customerId, 'Admin');
    $admin->givePermissionTo('manage-team');

    $this->actingAs($admin, 'customer_user')
        ->post(route('client.settings.users.store'), [
            'name' => 'Billing Person',
            'email' => 'billing-person@example.test',
            'role' => 'Accounts',
        ])
        ->assertForbidden();

    expect(CustomerUser::where('email', 'billing-person@example.test')->exists())->toBeFalse();
});

it('allows assigning a role whose permissions the manager holds', function () {
    $admin = Fixtures::user($this->customerId, 'Admin');
    $admin->givePermissionTo('manage-team');

    $this->actingAs($admin, 'customer_user')
        ->post(route('client.settings.users.store'), [
            'name' => 'HR Person',
            'email' => 'hr-person@example.test',
            'role' => 'HR',
        ])
        ->assertRedirect(route('client.settings.users'));

    expect(CustomerUser::where('email', 'hr-person@example.test')->exists())->toBeTrue();
});
