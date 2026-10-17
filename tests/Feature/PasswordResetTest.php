<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Tests\Support\Fixtures;

it('emails a reset link and stores a token for a known account', function () {
    $customer = Fixtures::customer();
    Fixtures::user($customer, 'Owner', ['email' => 'reset-me@example.test']);

    $this->post(route('client.forgot.submit'), ['email' => 'reset-me@example.test'])
        ->assertSessionHas('status');

    expect(DB::table('password_reset_tokens')->where('email', 'reset-me@example.test')->exists())->toBeTrue();

    $messages = Mail::getSymfonyTransport()->messages();
    expect($messages)->toHaveCount(1);
    expect($messages[0]->getOriginalMessage()->getTextBody())->toContain('/reset-password/');
});

it('gives the same response and sends nothing for an unknown email (no enumeration)', function () {
    $this->post(route('client.forgot.submit'), ['email' => 'nobody@example.test'])
        ->assertSessionHas('status');

    expect(DB::table('password_reset_tokens')->where('email', 'nobody@example.test')->exists())->toBeFalse();
    expect(Mail::getSymfonyTransport()->messages())->toHaveCount(0);
});

it('resets the password with a valid token and consumes it', function () {
    $customer = Fixtures::customer();
    $user = Fixtures::user($customer, 'Owner', [
        'email' => 'pw@example.test',
        'password' => bcrypt('old-password'),
    ]);

    $token = Password::broker('customer_users')->createToken($user);

    $this->post(route('client.reset.process'), [
        'token' => $token,
        'email' => 'pw@example.test',
        'password' => 'new-strong-password',
        'password_confirmation' => 'new-strong-password',
    ])->assertRedirect(route('client.login'))->assertSessionHas('success');

    $user->refresh();
    expect(Hash::check('new-strong-password', $user->password))->toBeTrue();
    // Single-use: the token row is deleted on a successful reset.
    expect(DB::table('password_reset_tokens')->where('email', 'pw@example.test')->exists())->toBeFalse();
});

it('rejects an invalid or expired token without changing the password', function () {
    $customer = Fixtures::customer();
    $user = Fixtures::user($customer, 'Owner', [
        'email' => 'pw2@example.test',
        'password' => bcrypt('old-password'),
    ]);

    $this->post(route('client.reset.process'), [
        'token' => 'totally-wrong-token',
        'email' => 'pw2@example.test',
        'password' => 'new-strong-password',
        'password_confirmation' => 'new-strong-password',
    ])->assertSessionHasErrors('email');

    $user->refresh();
    expect(Hash::check('old-password', $user->password))->toBeTrue();
});

it('requires a confirmed password of at least 8 characters', function () {
    $customer = Fixtures::customer();
    $user = Fixtures::user($customer, 'Owner', ['email' => 'pw3@example.test']);
    $token = Password::broker('customer_users')->createToken($user);

    $this->post(route('client.reset.process'), [
        'token' => $token,
        'email' => 'pw3@example.test',
        'password' => 'short',
        'password_confirmation' => 'nope',
    ])->assertSessionHasErrors('password');
});
