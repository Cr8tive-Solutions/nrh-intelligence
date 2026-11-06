<?php

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use Tests\Support\Fixtures;

/**
 * Sign-in hardening:
 *  - H2:  only private-range proxies may set X-Forwarded-For.
 *  - M8:  unknown emails cost the same hash check as known ones.
 *  - M9:  per-account lockout, and auth routes no longer share one throttle.
 *  - L11: no X-Powered-By header.
 */
beforeEach(function () {
    Fixtures::seedRoles();
    $this->customerId = Fixtures::customer();
});

function failedLogin(string $email, string $remoteAddr = '203.0.113.10', array $headers = []): TestResponse
{
    return test()->withServerVariables(['REMOTE_ADDR' => $remoteAddr])
        ->withHeaders($headers)
        ->post('/login', ['email' => $email, 'password' => 'Wrong-Passw0rd']);
}

// ── H2 — trusted proxies ─────────────────────────────────────────────────────

it('ignores X-Forwarded-For from an untrusted client, so the ip throttle holds', function () {
    foreach (range(1, 5) as $i) {
        failedLogin("spoof{$i}@example.test", '203.0.113.10', ['X-Forwarded-For' => "198.51.100.{$i}"])
            ->assertRedirect();
    }

    failedLogin('spoof6@example.test', '203.0.113.10', ['X-Forwarded-For' => '198.51.100.6'])
        ->assertTooManyRequests();
});

it('honours X-Forwarded-For from a private-range proxy', function () {
    $this->withServerVariables(['REMOTE_ADDR' => '172.18.0.2'])
        ->withHeaders(['X-Forwarded-For' => '198.51.100.77'])
        ->get('/login')
        ->assertOk();

    expect(request()->ip())->toBe('198.51.100.77');
});

// ── M8 — timing ──────────────────────────────────────────────────────────────

it('still runs a password hash check when the email is unknown', function () {
    Hash::partialMock()->shouldReceive('check')->once()->andReturnFalse();

    failedLogin('nobody-at-all@example.test')
        ->assertSessionHasErrors(['email' => 'Invalid email address or password.']);
});

// ── M9 — per-account lockout ─────────────────────────────────────────────────

it('locks an account after 10 failures even when they come from different ips', function () {
    Fixtures::user($this->customerId, 'Owner', ['email' => 'Target@Example.test']);

    foreach (range(1, 10) as $i) {
        failedLogin('target@example.test', "203.0.113.{$i}");
    }

    // The right password no longer helps, and the email's case does not matter.
    $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.99'])
        ->post('/login', ['email' => 'TARGET@example.test', 'password' => 'secret-password'])
        ->assertSessionHasErrors(['email' => 'Too many sign-in attempts. Please try again later.'])
        ->assertSessionMissing('2fa_pending_user_id');
});

it('clears the account failure count after a successful password', function () {
    $user = Fixtures::user($this->customerId, 'Owner');

    foreach (range(1, 4) as $i) {
        failedLogin($user->email, "203.0.113.{$i}");
    }

    $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.50'])
        ->post('/login', ['email' => $user->email, 'password' => 'secret-password'])
        ->assertRedirect(route('client.verification'));

    expect(RateLimiter::attempts('login-account:'.sha1(Str::lower($user->email))))->toBe(0);
});

it('does not lock the password reset form after failed logins from the same ip', function () {
    foreach (range(1, 5) as $i) {
        failedLogin("someone{$i}@example.test");
    }
    failedLogin('someone6@example.test')->assertTooManyRequests();

    $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.10'])
        ->post(route('client.forgot.submit'), ['email' => 'someone@example.test'])
        ->assertSessionHas('status');
});

// ── L11 — headers ────────────────────────────────────────────────────────────

it('strips the X-Powered-By header', function () {
    Route::get('/_test/powered-by', fn () => response('ok')->header('X-Powered-By', 'PHP/8.4'));

    $this->get('/_test/powered-by')->assertOk()->assertHeaderMissing('X-Powered-By');
});
