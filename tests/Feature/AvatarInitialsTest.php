<?php

use Tests\Support\Fixtures;

it('builds avatar initials from the first two words, or two letters of one word', function (?string $name, string $expected) {
    expect(initials($name, 'U'))->toBe($expected);
})->with([
    'two words' => ['Anwar Saad', 'AS'],
    'longer name' => ['Ahmad Faizal bin Rahman', 'AF'],
    'one word' => ['Kadboard', 'KA'],
    'extra spaces' => ['  anwar   saad ', 'AS'],
    'empty' => ['', 'U'],
    'null' => [null, 'U'],
]);

it('shows the signed-in user initials in the sidebar when no picture is uploaded', function () {
    Fixtures::seedRoles();
    $customerId = Fixtures::customer();
    $user = Fixtures::user($customerId, 'Owner', ['name' => 'Anwar Saad']);

    $this->actingAs($user, 'customer_user')
        ->withSession(['client_customer_id' => $customerId, 'client_user_id' => $user->id, 'client_user_name' => $user->name])
        ->get(route('client.settings.profile'))
        ->assertOk()
        ->assertSee('AS')
        ->assertDontSee('>AN<', false);
});
