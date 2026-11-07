<?php

use Tests\Support\Fixtures;

beforeEach(function () {
    $this->customerId = Fixtures::customer();
    $this->owner = Fixtures::user($this->customerId, 'Owner');
});

it('shows an empty state instead of a 404 when no agreement is on file', function () {
    $this->actingAs($this->owner, 'customer_user')
        ->get(route('client.settings.agreement'))
        ->assertOk()
        ->assertSee('No agreement on file yet');
});

it('shows the agreement details and key terms', function () {
    Fixtures::agreement($this->customerId, 'monthly', [
        'sla_tat' => '5 days',
        'terms' => json_encode(['Reports delivered by email']),
    ]);

    $this->actingAs($this->owner, 'customer_user')
        ->get(route('client.settings.agreement'))
        ->assertOk()
        ->assertSee('Agreement is active')
        ->assertSee('5 days')
        ->assertSee('Reports delivered by email');
});

it('marks an expired agreement as expired', function () {
    Fixtures::agreement($this->customerId, 'monthly', [
        'expiry_date' => now()->subDay()->toDateString(),
    ]);

    $this->actingAs($this->owner, 'customer_user')
        ->get(route('client.settings.agreement'))
        ->assertOk()
        ->assertSee('Agreement has expired')
        ->assertSee('Expired');
});
