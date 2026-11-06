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
        'password' => 'New-Strong-Passw0rd',
        'password_confirmation' => 'New-Strong-Passw0rd',
    ])->assertRedirect(route('client.login'))->assertSessionHas('success');

    $user->refresh();
    expect(Hash::check('New-Strong-Passw0rd', $user->password))->toBeTrue();
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
        'password' => 'New-Strong-Passw0rd',
        'password_confirmation' => 'New-Strong-Passw0rd',
    ])->assertSessionHasErrors('email');

    $user->refresh();
    expect(Hash::check('old-password', $user->password))->toBeTrue();
});

it('requires a confirmed password', function () {
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

it('rejects a password shorter than 12 characters or without mixed case and numbers', function (string $password) {
    $customer = Fixtures::customer();
    $user = Fixtures::user($customer, 'Owner', ['email' => 'pw4@example.test']);
    $token = Password::broker('customer_users')->createToken($user);

    $this->post(route('client.reset.process'), [
        'token' => $token,
        'email' => 'pw4@example.test',
        'password' => $password,
        'password_confirmation' => $password,
    ])->assertSessionHasErrors('password');
})->with([
    'too short' => ['Short1Pass'],
    'no uppercase' => ['all-lowercase-passw0rd'],
    'no number' => ['No-Numbers-Password'],
]);

it('gives the same failure message for an unknown email and a bad token', function () {
    $customer = Fixtures::customer();
    Fixtures::user($customer, 'Owner', ['email' => 'pw5@example.test']);

    $payload = [
        'token' => 'totally-wrong-token',
        'password' => 'New-Strong-Passw0rd',
        'password_confirmation' => 'New-Strong-Passw0rd',
    ];

    $message = 'This password reset link is invalid or has expired. Please request a new one.';

    $this->post(route('client.reset.process'), $payload + ['email' => 'pw5@example.test'])
        ->assertSessionHasErrors(['email' => $message]);

    $this->post(route('client.reset.process'), $payload + ['email' => 'nobody-here@example.test'])
        ->assertSessionHasErrors(['email' => $message]);
});
