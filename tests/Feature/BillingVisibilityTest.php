<?php

use Tests\Support\Fixtures;

/**
 * Who sees money in the Billing section.
 *
 * Amounts are gated on `view-prices`, the same permission that hides order-time
 * pricing. The transactions page used to print every amount plus a grand total
 * ungated, so any role with `view-billing` but not `view-prices` could read the
 * whole payment ledger — HR included.
 *
 * HR no longer has `view-billing` at all, but the view-level gate is kept so a
 * future role in that shape cannot leak it again.
 */
beforeEach(function () {
    Fixtures::seedRoles();

    $this->customerId = Fixtures::customer();
    Fixtures::agreement($this->customerId, 'monthly');

    // A distinctive amount so assertDontSee cannot pass by coincidence.
    Fixtures::transaction($this->customerId, ['amount' => 4321.99]);
});

it('shows transaction amounts to a role with view-prices', function () {
    $owner = Fixtures::user($this->customerId, 'Owner');

    $this->actingAs($owner, 'customer_user')
        ->get(route('client.billing.transactions'))
        ->assertOk()
        ->assertSee('4,321.99');
});

it('hides transaction amounts from a role without view-prices', function () {
    // Accounts minus view-prices is not a real role, so build the shape directly:
    // view-billing without view-prices is what used to leak.
    $user = Fixtures::user($this->customerId, 'Viewer');
    $user->givePermissionTo('view-billing');

    $this->actingAs($user, 'customer_user')
        ->get(route('client.billing.transactions'))
        ->assertOk()
        ->assertDontSee('4,321.99');
});

it('hides the transaction grand total from a role without view-prices', function () {
    $user = Fixtures::user($this->customerId, 'Viewer');
    $user->givePermissionTo('view-billing');

    $this->actingAs($user, 'customer_user')
        ->get(route('client.billing.transactions'))
        ->assertOk()
        ->assertDontSee('TOTAL ·', false);
});

it('keeps HR out of the billing section entirely', function () {
    $hr = Fixtures::user($this->customerId, 'HR');

    expect($hr->can('view-billing'))->toBeFalse();

    $this->actingAs($hr, 'customer_user')
        ->get(route('client.billing.transactions'))
        ->assertForbidden();

    $this->actingAs($hr, 'customer_user')
        ->get(route('client.billing.invoices'))
        ->assertForbidden();
});

it('leaves the Accounts role able to see billing', function () {
    $accounts = Fixtures::user($this->customerId, 'Accounts');

    expect($accounts->can('view-billing'))->toBeTrue()
        ->and($accounts->can('view-prices'))->toBeTrue();

    $this->actingAs($accounts, 'customer_user')
        ->get(route('client.billing.transactions'))
        ->assertOk()
        ->assertSee('4,321.99');
});

it('still lets HR do its own job', function () {
    $hr = Fixtures::user($this->customerId, 'HR');

    $this->actingAs($hr, 'customer_user')
        ->get(route('client.dashboard'))
        ->assertOk();

    $this->actingAs($hr, 'customer_user')
        ->get(route('client.requests.index'))
        ->assertOk();
});
