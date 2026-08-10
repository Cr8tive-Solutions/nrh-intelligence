<?php

use Tests\Support\Fixtures;

/**
 * Tenant isolation is the single most important property of the client portal:
 * one customer must never be able to read another customer's data, even with a
 * valid session and a correctly-encoded hashid for the other tenant's row.
 */
beforeEach(function () {
    // Tenant A — the attacker's own account.
    $this->customerA = Fixtures::customer(['name' => 'Tenant A']);
    $this->userA = Fixtures::user($this->customerA);
    Fixtures::agreement($this->customerA, 'monthly');

    // Tenant B — the victim.
    $this->customerB = Fixtures::customer(['name' => 'Tenant B']);
    $this->userB = Fixtures::user($this->customerB);
    Fixtures::agreement($this->customerB, 'monthly');

    $identityTypeId = Fixtures::identityType();

    $this->requestB = Fixtures::screeningRequest($this->customerB, $this->userB->id);
    Fixtures::candidate($this->requestB, $identityTypeId);
    $this->invoiceB = Fixtures::invoice($this->customerB);

    $this->requestA = Fixtures::screeningRequest($this->customerA, $this->userA->id);
});

it('redirects guests to the client login', function () {
    $this->get(route('client.requests.index'))->assertRedirect(route('client.login'));
});

it('lets a user open their own request', function () {
    $this->actingAs($this->userA, 'customer_user')
        ->get(route('client.requests.details', hid($this->requestA)))
        ->assertOk();
});

it('404s when a user opens another tenant\'s request', function () {
    $this->actingAs($this->userA, 'customer_user')
        ->get(route('client.requests.details', hid($this->requestB)))
        ->assertNotFound();
});

it('404s when a user opens another tenant\'s invoice', function () {
    $this->actingAs($this->userA, 'customer_user')
        ->get(route('client.billing.invoices.show', hid($this->invoiceB)))
        ->assertNotFound();
});

it('404s when a user downloads another tenant\'s invoice pdf', function () {
    $this->actingAs($this->userA, 'customer_user')
        ->get(route('client.billing.invoices.download', hid($this->invoiceB)))
        ->assertNotFound();
});

it('does not list another tenant\'s requests', function () {
    $response = $this->actingAs($this->userA, 'customer_user')
        ->get(route('client.requests.index'))
        ->assertOk();

    $refB = DB::table('screening_requests')->where('id', $this->requestB)->value('reference');
    $response->assertDontSee($refB);
});

it('404s on a hashid that decodes to nothing', function () {
    $this->actingAs($this->userA, 'customer_user')
        ->get(route('client.requests.details', 'not-a-real-hashid'))
        ->assertNotFound();
});

it('keeps session tenant keys in sync with the auth guard', function () {
    $this->actingAs($this->userA, 'customer_user')
        ->get(route('client.requests.index'))
        ->assertOk();

    // ClientAuth rewrites these on every request so controllers can't be
    // tricked by a stale or forged session value.
    expect(session('client_customer_id'))->toBe($this->customerA)
        ->and(session('client_user_id'))->toBe($this->userA->id);
});

it('ignores a forged tenant id in the session', function () {
    $this->actingAs($this->userA, 'customer_user')
        ->withSession(['client_customer_id' => $this->customerB])
        ->get(route('client.requests.details', hid($this->requestB)))
        ->assertNotFound();
});
