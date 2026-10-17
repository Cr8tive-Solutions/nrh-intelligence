<?php

use App\Models\CustomerUser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Tests\Support\Fixtures;

/**
 * Covers the "session & account-takeover" hardening batch:
 *  - M3: a deactivated user loses access immediately (ClientAuth re-check).
 *  - M5: changing a password evicts other sessions / remember-me.
 *  - M4: email changes need the current password and a confirmation click.
 *  - L2: every user can reach their own security page.
 *  - M2: the 2FA code cannot be brute-forced within its window.
 */
beforeEach(function () {
    Fixtures::seedRoles();
    $this->customerId = Fixtures::customer();
});

// ── M3 — deactivated users ───────────────────────────────────────────────────

it('logs out a user whose account has been deactivated', function () {
    $user = Fixtures::user($this->customerId, 'Owner', ['status' => 'inactive']);

    $this->actingAs($user, 'customer_user')
        ->get('/dashboard')
        ->assertRedirect(route('client.login'));
});

it('still serves an active user', function () {
    $user = Fixtures::user($this->customerId, 'Owner', ['status' => 'active']);

    $this->actingAs($user, 'customer_user')
        ->get('/dashboard')
        ->assertOk();
});

it('rotates the remember token when a user is deactivated', function () {
    $owner = Fixtures::user($this->customerId, 'Owner');
    $member = Fixtures::user($this->customerId, 'HR', ['remember_token' => 'original-token']);

    $this->actingAs($owner, 'customer_user')
        ->put('/settings/users/'.hid($member->id), [
            'name' => $member->name,
            'email' => $member->email,
            'role' => 'HR',
            'status' => 'inactive',
        ])
        ->assertRedirect();

    expect(CustomerUser::find($member->id)->remember_token)->not->toBe('original-token');
});

// ── M5 — password change ─────────────────────────────────────────────────────

it('changes the password and rotates the remember token', function () {
    $user = Fixtures::user($this->customerId, 'Owner', ['remember_token' => 'stale-token']);

    $this->actingAs($user, 'customer_user')
        ->post('/settings/security', [
            'current_password' => 'secret-password',
            'password' => 'a-brand-new-password',
            'password_confirmation' => 'a-brand-new-password',
        ])
        ->assertRedirect();

    $fresh = CustomerUser::find($user->id);
    expect(Hash::check('a-brand-new-password', $fresh->password))->toBeTrue()
        ->and($fresh->remember_token)->not->toBe('stale-token')
        ->and($fresh->remember_token)->not->toBeNull();
});

it('rejects a password change with the wrong current password', function () {
    $user = Fixtures::user($this->customerId, 'Owner');

    $this->actingAs($user, 'customer_user')
        ->post('/settings/security', [
            'current_password' => 'wrong-password',
            'password' => 'a-brand-new-password',
            'password_confirmation' => 'a-brand-new-password',
        ])
        ->assertSessionHasErrors('current_password');

    expect(Hash::check('secret-password', CustomerUser::find($user->id)->password))->toBeTrue();
});

// ── L2 — security page is per-user ───────────────────────────────────────────

it('lets a user without manage-settings reach their own security page', function () {
    $user = Fixtures::user($this->customerId, 'Viewer');

    $this->actingAs($user, 'customer_user')->get('/settings/security')->assertOk();
});

it('still gates the company account page behind manage-settings', function () {
    $user = Fixtures::user($this->customerId, 'Viewer');

    $this->actingAs($user, 'customer_user')->get('/settings/account')->assertForbidden();
});

// ── M4 — email change ────────────────────────────────────────────────────────

it('does not change the email without the current password', function () {
    $user = Fixtures::user($this->customerId, 'Owner');
    $original = $user->email;

    $this->actingAs($user, 'customer_user')
        ->post('/settings/profile', [
            'name' => $user->name,
            'email' => 'attacker@evil.test',
        ])
        ->assertSessionHasErrors('current_password');

    expect(CustomerUser::find($user->id)->email)->toBe($original);
});

it('does not switch the email immediately even with the correct password', function () {
    $user = Fixtures::user($this->customerId, 'Owner');
    $original = $user->email;

    $this->actingAs($user, 'customer_user')
        ->post('/settings/profile', [
            'name' => $user->name,
            'email' => 'new-address@example.test',
            'current_password' => 'secret-password',
        ])
        ->assertRedirect();

    // Still the old address until the confirmation link is followed.
    expect(CustomerUser::find($user->id)->email)->toBe($original);
});

it('applies the email change only via a valid signed confirmation link', function () {
    $user = Fixtures::user($this->customerId, 'Owner');

    $url = URL::temporarySignedRoute(
        'client.settings.profile.email.confirm',
        now()->addHour(),
        ['user' => $user->id, 'email' => 'confirmed@example.test'],
    );

    $this->actingAs($user, 'customer_user')->get($url)->assertRedirect();

    expect(CustomerUser::find($user->id)->email)->toBe('confirmed@example.test');
});

it('rejects an unsigned email confirmation link', function () {
    $user = Fixtures::user($this->customerId, 'Owner');

    $this->actingAs($user, 'customer_user')
        ->get('/settings/profile/email/confirm?user='.$user->id.'&email=confirmed@example.test')
        ->assertForbidden();

    expect(CustomerUser::find($user->id)->email)->not->toBe('confirmed@example.test');
});

it('allows a name-only profile update without the current password', function () {
    $user = Fixtures::user($this->customerId, 'Owner');

    $this->actingAs($user, 'customer_user')
        ->post('/settings/profile', [
            'name' => 'New Display Name',
            'email' => $user->email,
        ])
        ->assertRedirect();

    expect(CustomerUser::find($user->id)->name)->toBe('New Display Name');
});

// ── M2 — 2FA brute-force limit ───────────────────────────────────────────────

it('discards the pending login after too many wrong 2fa codes', function () {
    $user = Fixtures::user($this->customerId, 'Owner');

    // Pretend the user has already made 4 wrong guesses; the 5th must end it.
    $this->withSession([
        '2fa_pending_user_id' => $user->id,
        '2fa_pending_customer_id' => $user->customer_id,
        '2fa_method' => 'email',
        '2fa_code' => '123456',
        '2fa_expires_at' => now()->addMinutes(5)->timestamp,
        '2fa_email' => $user->email,
        '2fa_attempts' => 4,
    ])->post('/verification', ['code' => '654321'])
        ->assertRedirect(route('client.login'))
        ->assertSessionMissing('2fa_pending_user_id');
});

it('keeps the pending login after a single wrong 2fa code', function () {
    $user = Fixtures::user($this->customerId, 'Owner');

    $this->withSession([
        '2fa_pending_user_id' => $user->id,
        '2fa_pending_customer_id' => $user->customer_id,
        '2fa_method' => 'email',
        '2fa_code' => '123456',
        '2fa_expires_at' => now()->addMinutes(5)->timestamp,
        '2fa_email' => $user->email,
        '2fa_attempts' => 0,
    ])->post('/verification', ['code' => '654321'])
        ->assertSessionHasErrors('code')
        ->assertSessionHas('2fa_pending_user_id', $user->id);
});
