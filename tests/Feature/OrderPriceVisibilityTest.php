<?php

use Spatie\Permission\Models\Role;
use Tests\Support\Fixtures;

/**
 * Order-time pricing is gated on `view-prices`.
 *
 * The HR role deliberately lacks it: HR submits orders without seeing cost, and
 * the Accounts/Owner side deals with money. The wizard hides prices with
 * x-show, but that alone would leave the figures in the page source — the
 * controller also zeroes them, which is what these tests pin down.
 */
beforeEach(function () {
    Fixtures::seedRoles();

    $this->customerId = Fixtures::customer();
    Fixtures::agreement($this->customerId, 'monthly');
    $this->countryId = Fixtures::country();

    // A distinctive price so assertDontSee cannot pass by coincidence.
    $this->scopeId = Fixtures::scopeType($this->countryId, ['price' => 1234.56]);
});

it('does not leak scope prices to a role without view-prices', function () {
    $hr = Fixtures::user($this->customerId, 'HR');

    $response = $this->actingAs($hr, 'customer_user')
        ->get(route('client.request.global'))
        ->assertOk();

    // Neither rendered nor embedded in the Alpine payload.
    $response->assertDontSee('1234.56');
});

it('shows scope prices to a role with view-prices', function () {
    $owner = Fixtures::user($this->customerId, 'Owner');

    $this->actingAs($owner, 'customer_user')
        ->get(route('client.request.global'))
        ->assertOk()
        ->assertSee('1234.56');
});

it('tells a price-blind user who handles pricing', function () {
    $hr = Fixtures::user($this->customerId, 'HR');

    $this->actingAs($hr, 'customer_user')
        ->get(route('client.request.global'))
        ->assertOk()
        ->assertSee('Pricing is handled by your Accounts team', false);
});

it('does not leak package totals to a role without view-prices', function () {
    $packageId = Fixtures::package($this->customerId, $this->countryId, ['name' => 'Standard Screening']);
    Fixtures::attachPackageScope($packageId, $this->scopeId);

    $hr = Fixtures::user($this->customerId, 'HR');

    $this->actingAs($hr, 'customer_user')
        ->get(route('client.request.global'))
        ->assertOk()
        // The package is still offered — only its price is withheld.
        ->assertSee('Standard Screening')
        ->assertDontSee('1234.56');
});

it('still lets a price-blind user submit an order', function () {
    $identityTypeId = Fixtures::identityType();
    $hr = Fixtures::user($this->customerId, 'HR');

    $this->actingAs($hr, 'customer_user')
        ->post(route('client.request.submit'), [
            'screening_type' => 'employment_global',
            'consent' => '1',
            'cart_data' => json_encode([['id' => $this->scopeId]]),
            'candidates_data' => json_encode([[
                'identity_type_id' => $identityTypeId,
                'name' => 'Ali bin Abu',
                'identity_number' => '900101-01-1234',
                'nationality' => 'Malaysian',
            ]]),
        ])
        ->assertRedirect(route('client.request.success'));
});

it('has retired the Member role', function () {
    $names = Role::where('guard_name', 'customer_user')
        ->pluck('name')
        ->all();

    expect($names)->not->toContain('Member')
        ->and($names)->toContain('HR');
});
