<?php

use App\Models\CustomerUser;
use PragmaRX\Google2FA\Google2FA;
use Tests\Support\Fixtures;

/**
 * Authenticator-app (TOTP) 2FA: enrollment, the app-vs-email login branch, and
 * code verification.
 */
beforeEach(function () {
    Fixtures::seedRoles();
    $this->customerId = Fixtures::customer();
    $this->google2fa = new Google2FA;
});

/**
 * Create a user with a confirmed authenticator-app factor. The two_factor_*
 * columns are deliberately not mass-assignable, so they are set directly.
 */
function enrolledAppUser(int $customerId, string $secret): CustomerUser
{
    $user = Fixtures::user($customerId, 'Owner');
    $user->two_factor_secret = $secret;
    $user->two_factor_confirmed_at = now();
    $user->save();

    return $user->refresh();
}

// ── Enrollment ───────────────────────────────────────────────────────────────

it('starts enrollment by stashing a setup secret and rendering a qr', function () {
    $user = Fixtures::user($this->customerId, 'Owner');

    $this->actingAs($user, 'customer_user')
        ->post('/settings/security/2fa/enable')
        ->assertRedirect()
        ->assertSessionHas('2fa_setup_secret');

    // Not yet active until a code confirms it.
    expect(CustomerUser::find($user->id)->hasTwoFactorAppEnabled())->toBeFalse();

    // The QR renders as inline SVG while the setup secret is in the session.
    $this->actingAs($user, 'customer_user')
        ->get('/settings/security')
        ->assertOk()
        ->assertSee('<svg', false);
});

it('enables the app after a valid confirmation code', function () {
    $user = Fixtures::user($this->customerId, 'Owner');
    $secret = $this->google2fa->generateSecretKey();

    $this->withSession(['2fa_setup_secret' => $secret])
        ->actingAs($user, 'customer_user')
        ->post('/settings/security/2fa/confirm', [
            'code' => $this->google2fa->getCurrentOtp($secret),
        ])
        ->assertRedirect()
        ->assertSessionMissing('2fa_setup_secret');

    $fresh = CustomerUser::find($user->id);
    expect($fresh->hasTwoFactorAppEnabled())->toBeTrue()
        ->and($fresh->two_factor_secret)->toBe($secret);
});

it('rejects an invalid confirmation code', function () {
    $user = Fixtures::user($this->customerId, 'Owner');
    $secret = $this->google2fa->generateSecretKey();

    $this->withSession(['2fa_setup_secret' => $secret])
        ->actingAs($user, 'customer_user')
        ->post('/settings/security/2fa/confirm', ['code' => '000000'])
        ->assertSessionHasErrors('code');

    expect(CustomerUser::find($user->id)->hasTwoFactorAppEnabled())->toBeFalse();
});

it('disables the app only with the correct password', function () {
    $secret = $this->google2fa->generateSecretKey();
    $user = enrolledAppUser($this->customerId, $secret);

    // Wrong password leaves it enabled.
    $this->actingAs($user, 'customer_user')
        ->post('/settings/security/2fa/disable', ['current_password' => 'nope'])
        ->assertSessionHasErrors('current_password');
    expect(CustomerUser::find($user->id)->hasTwoFactorAppEnabled())->toBeTrue();

    // Correct password disables it.
    $this->actingAs($user, 'customer_user')
        ->post('/settings/security/2fa/disable', ['current_password' => 'secret-password'])
        ->assertRedirect();
    expect(CustomerUser::find($user->id)->hasTwoFactorAppEnabled())->toBeFalse();
});

// ── Login branch ─────────────────────────────────────────────────────────────

it('sends an app-enrolled user to the app challenge without emailing a code', function () {
    $secret = $this->google2fa->generateSecretKey();
    $user = enrolledAppUser($this->customerId, $secret);

    $this->post('/login', ['email' => $user->email, 'password' => 'secret-password'])
        ->assertRedirect(route('client.verification'))
        ->assertSessionHas('2fa_method', 'app');
});

it('logs in with a valid authenticator app code', function () {
    $secret = $this->google2fa->generateSecretKey();
    $user = enrolledAppUser($this->customerId, $secret);

    $this->withSession([
        '2fa_pending_user_id' => $user->id,
        '2fa_pending_customer_id' => $user->customer_id,
        '2fa_method' => 'app',
        '2fa_email' => $user->email,
        '2fa_remember' => false,
        '2fa_attempts' => 0,
    ])->post('/verification', ['code' => $this->google2fa->getCurrentOtp($secret)])
        ->assertRedirect(route('client.dashboard'));

    expect(auth('customer_user')->id())->toBe($user->id);
});

it('rejects a wrong authenticator app code', function () {
    $secret = $this->google2fa->generateSecretKey();
    $user = enrolledAppUser($this->customerId, $secret);

    $this->withSession([
        '2fa_pending_user_id' => $user->id,
        '2fa_pending_customer_id' => $user->customer_id,
        '2fa_method' => 'app',
        '2fa_email' => $user->email,
        '2fa_remember' => false,
        '2fa_attempts' => 0,
    ])->post('/verification', ['code' => '000000'])
        ->assertSessionHasErrors('code');

    expect(auth('customer_user')->check())->toBeFalse();
});

it('falls back to the email method for a user without the app', function () {
    $user = Fixtures::user($this->customerId, 'Owner');

    $this->post('/login', ['email' => $user->email, 'password' => 'secret-password'])
        ->assertRedirect(route('client.verification'))
        ->assertSessionHas('2fa_method', 'email')
        ->assertSessionHas('2fa_code');
});
