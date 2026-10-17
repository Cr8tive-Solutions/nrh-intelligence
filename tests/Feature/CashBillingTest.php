<?php

use App\Models\Invoice;
use App\Models\ScreeningRequest;
use Illuminate\Support\Facades\DB;
use Tests\Support\Fixtures;

beforeEach(function () {
    $this->countryId = Fixtures::country();
    $this->identityTypeId = Fixtures::identityType();
    $this->customerId = Fixtures::customer();
    $this->user = Fixtures::user($this->customerId);
});

/** Same payload shape RequestSubmissionTest uses. */
function cashSubmitPayload(int $scopeTypeId, int $identityTypeId): array
{
    return [
        'screening_type' => 'employment_global',
        'consent' => '1',
        'cart_data' => json_encode([['id' => $scopeTypeId]]),
        'candidates_data' => json_encode([[
            'identity_type_id' => $identityTypeId,
            'name' => 'Ali bin Abu',
            'identity_number' => '900101-01-1234',
            'nationality' => 'Malaysian',
        ]]),
    ];
}

it('adds SST on top of the cash subtotal', function () {
    Fixtures::agreement($this->customerId, 'per_request');
    $scopeId = Fixtures::scopeType($this->countryId, ['price' => 100.00]);
    $requestId = Fixtures::screeningRequest($this->customerId, $this->user->id);
    $candidateId = Fixtures::candidate($requestId, $this->identityTypeId);
    DB::table('candidate_scope_type')->insert([
        'request_candidate_id' => $candidateId, 'scope_type_id' => $scopeId,
        'status' => 'new', 'assigned_at' => now(),
    ]);

    $request = ScreeningRequest::find($requestId);

    expect($request->cashSubtotal())->toBe(100.0)
        ->and($request->cashTax())->toBe(6.0)
        ->and($request->cashTotal())->toBe(106.0);
});

it('refuses to submit a request containing an unpriced price-on-request scope', function () {
    Fixtures::agreement($this->customerId, 'monthly');
    $porScopeId = Fixtures::scopeType($this->countryId, [
        'price' => 0.00, 'price_on_request' => true,
    ]);

    $this->actingAs($this->user, 'customer_user')
        ->post(route('client.request.submit'), cashSubmitPayload($porScopeId, $this->identityTypeId))
        ->assertSessionHasErrors('cart_data');

    expect(ScreeningRequest::where('customer_id', $this->customerId)->count())->toBe(0);
});

it('accepts a price-on-request scope once a customer price exists', function () {
    Fixtures::agreement($this->customerId, 'monthly');
    $porScopeId = Fixtures::scopeType($this->countryId, [
        'price' => 0.00, 'price_on_request' => true,
    ]);
    DB::table('customer_scope_prices')->insert([
        'customer_id' => $this->customerId, 'scope_type_id' => $porScopeId,
        'price' => 250.00, 'created_at' => now(), 'updated_at' => now(),
    ]);

    $this->actingAs($this->user, 'customer_user')
        ->post(route('client.request.submit'), cashSubmitPayload($porScopeId, $this->identityTypeId))
        ->assertRedirect(route('client.request.success'));

    expect(ScreeningRequest::where('customer_id', $this->customerId)->count())->toBe(1);
});

it('auto-generates an unpaid invoice linked to a cash request on submit', function () {
    Fixtures::agreement($this->customerId, 'per_request');
    $scopeId = Fixtures::scopeType($this->countryId, ['price' => 100.00, 'name' => 'Criminal Record Check']);

    $this->actingAs($this->user, 'customer_user')
        ->post(route('client.request.submit'), cashSubmitPayload($scopeId, $this->identityTypeId))
        ->assertSessionHas('status');

    $request = ScreeningRequest::where('customer_id', $this->customerId)->firstOrFail();
    expect($request->invoice_id)->not->toBeNull()
        ->and($request->status)->toBe('new'); // stays new until the invoice is paid+verified

    $invoice = Invoice::findOrFail($request->invoice_id);
    expect($invoice->status)->toBe('unpaid')
        ->and((float) $invoice->subtotal)->toBe(100.0)
        ->and((float) $invoice->tax)->toBe(6.0)
        ->and((float) $invoice->total)->toBe(106.0)
        ->and($invoice->number)->toStartWith('INV-')
        ->and($invoice->issued_at->toDateString())->toBe($invoice->due_at->toDateString()); // due immediately

    // One line per candidate × scope, carrying the candidate's name.
    expect($invoice->items()->count())->toBe(1);
    $item = $invoice->items()->first();
    expect($item->description)->toContain('Ali bin Abu')
        ->and($item->description)->toContain('Criminal Record Check')
        ->and((float) $item->unit_price)->toBe(100.0);
});

it('does not generate an invoice for a monthly (credit) request', function () {
    Fixtures::agreement($this->customerId, 'monthly');
    $scopeId = Fixtures::scopeType($this->countryId, ['price' => 100.00]);

    $this->actingAs($this->user, 'customer_user')
        ->post(route('client.request.submit'), cashSubmitPayload($scopeId, $this->identityTypeId))
        ->assertRedirect(route('client.request.success'));

    $request = ScreeningRequest::where('customer_id', $this->customerId)->firstOrFail();
    expect($request->invoice_id)->toBeNull()
        ->and(Invoice::where('customer_id', $this->customerId)->count())->toBe(0);
});

it('creates one invoice line per candidate when a cash request has several', function () {
    Fixtures::agreement($this->customerId, 'per_request');
    $scopeId = Fixtures::scopeType($this->countryId, ['price' => 80.00, 'name' => 'Bankruptcy Search']);

    $payload = [
        'screening_type' => 'employment_global',
        'consent' => '1',
        'cart_data' => json_encode([['id' => $scopeId]]),
        'candidates_data' => json_encode([
            ['identity_type_id' => $this->identityTypeId, 'name' => 'Ali bin Abu',  'identity_number' => '900101-01-1234', 'nationality' => 'Malaysian'],
            ['identity_type_id' => $this->identityTypeId, 'name' => 'Siti binti Yu', 'identity_number' => '910202-02-2345', 'nationality' => 'Malaysian'],
        ]),
    ];

    $this->actingAs($this->user, 'customer_user')
        ->post(route('client.request.submit'), $payload)
        ->assertSessionHas('status');

    $invoice = Invoice::where('customer_id', $this->customerId)->firstOrFail();
    expect($invoice->items()->count())->toBe(2)
        ->and((float) $invoice->subtotal)->toBe(160.0)
        ->and($invoice->items()->pluck('description')->implode('|'))->toContain('Ali bin Abu')
        ->and($invoice->items()->pluck('description')->implode('|'))->toContain('Siti binti Yu');
});

it('shows the invoice payment pointer on the cash request details page', function () {
    Fixtures::agreement($this->customerId, 'per_request');
    $scopeId = Fixtures::scopeType($this->countryId, ['price' => 100.00, 'name' => 'Criminal Record Check']);

    $this->actingAs($this->user, 'customer_user')
        ->post(route('client.request.submit'), cashSubmitPayload($scopeId, $this->identityTypeId));

    $request = ScreeningRequest::where('customer_id', $this->customerId)->firstOrFail();
    $invoice = Invoice::findOrFail($request->invoice_id);

    $this->actingAs($this->user, 'customer_user')
        ->get(route('client.requests.details', hid($request->id)))
        ->assertOk()
        ->assertSee('Payment required')
        ->assertSee($invoice->number);
});

it('bills a cash package order at the bundle price per candidate', function () {
    Fixtures::agreement($this->customerId, 'per_request');
    $scopeA = Fixtures::scopeType($this->countryId, ['price' => 100.00]);
    $scopeB = Fixtures::scopeType($this->countryId, ['price' => 100.00]);
    $packageId = Fixtures::package($this->customerId, $this->countryId, ['name' => 'Exec Pack', 'price' => 300.00]);
    Fixtures::attachPackageScope($packageId, $scopeA);
    Fixtures::attachPackageScope($packageId, $scopeB);

    $payload = [
        'screening_type' => 'employment_global',
        'consent' => '1',
        'cart_data' => json_encode([['id' => $scopeA], ['id' => $scopeB]]),
        'candidates_data' => json_encode([
            ['identity_type_id' => $this->identityTypeId, 'name' => 'Ali bin Abu', 'identity_number' => '900101-01-1234', 'nationality' => 'Malaysian'],
            ['identity_type_id' => $this->identityTypeId, 'name' => 'Siti binti Kassim', 'identity_number' => '920202-02-2222', 'nationality' => 'Malaysian'],
        ]),
        'package_id' => $packageId,
    ];

    $this->actingAs($this->user, 'customer_user')
        ->post(route('client.request.submit'), $payload)
        ->assertRedirect(); // cash requests land on the request/invoice page

    $request = ScreeningRequest::where('customer_id', $this->customerId)->firstOrFail();

    // Bundle 300 × 2 candidates = 600, not 4 scope-rows × 100 = 400.
    expect($request->package_id)->toBe($packageId)
        ->and($request->cashSubtotal())->toBe(600.0)
        ->and($request->cashTotal())->toBe(636.0);

    $invoice = Invoice::findOrFail($request->invoice_id);
    expect((float) $invoice->subtotal)->toBe(600.0)
        ->and((float) $invoice->total)->toBe(636.0)
        ->and($invoice->items()->count())->toBe(2); // one bundle line per candidate

    $first = $invoice->items()->first();
    expect((float) $first->unit_price)->toBe(300.0)
        ->and($first->description)->toContain('Exec Pack');
});

it('accepts a priced package even if it bundles a price-on-request scope', function () {
    Fixtures::agreement($this->customerId, 'per_request');
    $por = Fixtures::scopeType($this->countryId, ['price' => 0.00, 'price_on_request' => true]);
    $packageId = Fixtures::package($this->customerId, $this->countryId, ['name' => 'Bundle', 'price' => 275.00]);
    Fixtures::attachPackageScope($packageId, $por);

    $payload = [
        'screening_type' => 'employment_global',
        'consent' => '1',
        'cart_data' => json_encode([['id' => $por]]),
        'candidates_data' => json_encode([
            ['identity_type_id' => $this->identityTypeId, 'name' => 'Ali bin Abu', 'identity_number' => '900101-01-1234', 'nationality' => 'Malaysian'],
        ]),
        'package_id' => $packageId,
    ];

    // The per-scope unpriced guard must NOT block a priced package.
    $this->actingAs($this->user, 'customer_user')
        ->post(route('client.request.submit'), $payload)
        ->assertSessionHasNoErrors()
        ->assertRedirect(); // cash requests land on the request/invoice page

    $request = ScreeningRequest::where('customer_id', $this->customerId)->firstOrFail();
    expect($request->package_id)->toBe($packageId)
        ->and($request->cashSubtotal())->toBe(275.0);
});
