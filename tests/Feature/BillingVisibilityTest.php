<?php

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
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

// ── Payment slips need manage-billing to change ──────────────────────────────

it('forbids uploading or removing a payment slip without manage-billing', function () {
    $customerId = Fixtures::customer();
    Fixtures::agreement($customerId, 'per_request');
    // Admin has view-prices but not manage-billing.
    $admin = Fixtures::user($customerId, 'Admin');
    $requestId = Fixtures::screeningRequest($customerId, $admin->id, ['status' => 'new']);

    $this->actingAs($admin, 'customer_user')
        ->post(route('client.requests.payment-slip.store', hid($requestId)), [
            'payment_slip' => UploadedFile::fake()->create('slip.pdf', 10, 'application/pdf'),
        ])
        ->assertForbidden();

    $this->actingAs($admin, 'customer_user')
        ->delete(route('client.requests.payment-slip.destroy', hid($requestId)))
        ->assertForbidden();
});

it('lets a view-prices user without manage-billing reach the slip download', function () {
    $customerId = Fixtures::customer();
    Fixtures::agreement($customerId, 'per_request');
    $admin = Fixtures::user($customerId, 'Admin');
    $requestId = Fixtures::screeningRequest($customerId, $admin->id, ['status' => 'new']);

    // Past the permission gate; there is simply no slip uploaded yet.
    $this->actingAs($admin, 'customer_user')
        ->get(route('client.requests.payment-slip.download', hid($requestId)))
        ->assertNotFound();
});

it('hides the slip upload form from a user without manage-billing', function () {
    $customerId = Fixtures::customer();
    Fixtures::agreement($customerId, 'per_request');
    $admin = Fixtures::user($customerId, 'Admin');
    $owner = Fixtures::user($customerId, 'Owner');
    $requestId = Fixtures::screeningRequest($customerId, $owner->id, ['status' => 'new']);

    $this->actingAs($admin, 'customer_user')
        ->get(route('client.requests.details', hid($requestId)))
        ->assertOk()
        ->assertSee('Payment details')
        ->assertDontSee('Upload slip');

    $this->flushSession();

    $this->actingAs($owner, 'customer_user')
        ->get(route('client.requests.details', hid($requestId)))
        ->assertOk()
        ->assertSee('Upload slip');
});

// ── Money in notifications and on the request page ───────────────────────────

it('keeps billing notifications from a role without view-billing', function () {
    Fixtures::invoice($this->customerId, ['total' => 8765.43]);
    $hr = Fixtures::user($this->customerId, 'HR');

    $this->actingAs($hr, 'customer_user')
        ->get(route('client.notifications'))
        ->assertOk()
        ->assertDontSee('8,765.43')
        ->assertDontSee('4,321.99')
        ->assertDontSee('is unpaid');
});

it('shows billing notifications without amounts to view-billing without view-prices', function () {
    Fixtures::invoice($this->customerId, ['total' => 8765.43]);
    $user = Fixtures::user($this->customerId, 'Viewer');
    $user->givePermissionTo('view-billing');

    $this->actingAs($user, 'customer_user')
        ->get(route('client.notifications'))
        ->assertOk()
        ->assertSee('is unpaid')
        ->assertDontSee('8,765.43')
        ->assertDontSee('4,321.99');
});

it('shows billing notifications with amounts to the Accounts role', function () {
    Fixtures::invoice($this->customerId, ['total' => 8765.43]);
    $accounts = Fixtures::user($this->customerId, 'Accounts');

    $this->actingAs($accounts, 'customer_user')
        ->get(route('client.notifications'))
        ->assertOk()
        ->assertSee('8,765.43')
        ->assertSee('4,321.99');
});

it('hides the invoice total on the request page from a role without view-prices', function () {
    $customerId = Fixtures::customer();
    Fixtures::agreement($customerId, 'per_request');
    $hr = Fixtures::user($customerId, 'HR');
    $invoiceId = Fixtures::invoice($customerId, ['total' => 2468.13]);
    $requestId = Fixtures::screeningRequest($customerId, $hr->id, ['status' => 'new', 'invoice_id' => $invoiceId]);

    $this->actingAs($hr, 'customer_user')
        ->get(route('client.requests.details', hid($requestId)))
        ->assertOk()
        ->assertSee('Payment required')
        ->assertDontSee('2,468.13');

    $owner = Fixtures::user($customerId, 'Owner');
    $this->flushSession();

    $this->actingAs($owner, 'customer_user')
        ->get(route('client.requests.details', hid($requestId)))
        ->assertOk()
        ->assertSee('2,468.13');
});

// ── Receipts only for open invoices ──────────────────────────────────────────

// The invoices.status check constraint currently allows only unpaid, overdue
// and paid, so a cancelled/void invoice cannot be built here; the controller
// allow-lists the open statuses so any future closed status is refused too.
it('refuses a payment receipt for a paid invoice', function () {
    $accounts = Fixtures::user($this->customerId, 'Accounts');
    $invoiceId = Fixtures::invoice($this->customerId, ['status' => 'paid']);

    $this->actingAs($accounts, 'customer_user')
        ->post(route('client.billing.invoices.receipts.store', hid($invoiceId)), [
            'receipt_file' => UploadedFile::fake()->create('receipt.pdf', 10, 'application/pdf'),
        ])
        ->assertSessionHas('error');

    expect(DB::table('invoice_payment_receipts')->where('invoice_id', $invoiceId)->exists())->toBeFalse();
});

it('still accepts a payment receipt for an overdue invoice', function () {
    $accounts = Fixtures::user($this->customerId, 'Accounts');
    $invoiceId = Fixtures::invoice($this->customerId, ['status' => 'overdue']);

    $this->actingAs($accounts, 'customer_user')
        ->post(route('client.billing.invoices.receipts.store', hid($invoiceId)), [
            'receipt_file' => UploadedFile::fake()->create('receipt.pdf', 10, 'application/pdf'),
        ])
        ->assertSessionHas('status');

    expect(DB::table('invoice_payment_receipts')->where('invoice_id', $invoiceId)->exists())->toBeTrue();
});
