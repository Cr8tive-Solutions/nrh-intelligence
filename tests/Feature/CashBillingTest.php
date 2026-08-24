<?php

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
