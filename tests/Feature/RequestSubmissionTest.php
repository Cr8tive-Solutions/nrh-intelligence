<?php

use App\Models\ScreeningRequest;
use Illuminate\Support\Facades\DB;
use Tests\Support\Fixtures;

beforeEach(function () {
    $this->countryId = Fixtures::country();
    $this->identityTypeId = Fixtures::identityType();
    $this->scopeTypeId = Fixtures::scopeType($this->countryId);
});

/** Build the form payload CreateRequestController::submit expects. */
function submitPayload(int $scopeTypeId, int $identityTypeId, array $overrides = []): array
{
    return array_merge([
        'screening_type' => 'employment_global',
        'consent' => '1',
        'cart_data' => json_encode([['id' => $scopeTypeId]]),
        'candidates_data' => json_encode([[
            'identity_type_id' => $identityTypeId,
            'name' => 'Ali bin Abu',
            'identity_number' => '900101-01-1234',
            'nationality' => 'Malaysian',
        ]]),
    ], $overrides);
}

it('creates the request, candidate, scope pivot and consent record', function () {
    $customerId = Fixtures::customer();
    $user = Fixtures::user($customerId);
    Fixtures::agreement($customerId, 'monthly');

    $this->actingAs($user, 'customer_user')
        ->post(route('client.request.submit'), submitPayload($this->scopeTypeId, $this->identityTypeId))
        ->assertRedirect(route('client.request.success'));

    $request = ScreeningRequest::where('customer_id', $customerId)->firstOrFail();
    expect($request->reference)->toStartWith('REQ-')
        ->and($request->type)->toBe('employment_global');

    $candidate = DB::table('request_candidates')->where('screening_request_id', $request->id)->first();
    expect($candidate->name)->toBe('Ali bin Abu');

    // Each candidate gets a pivot row per selected scope, seeded as 'new'.
    $pivot = DB::table('candidate_scope_type')->where('request_candidate_id', $candidate->id)->first();
    expect($pivot->scope_type_id)->toBe($this->scopeTypeId)
        ->and($pivot->status)->toBe('new');

    // PDPA: a consent record is written for every candidate.
    $consent = DB::table('consent_records')->where('request_candidate_id', $candidate->id)->first();
    expect($consent)->not->toBeNull()
        ->and($consent->evidence_type)->toBe('digital_form')
        ->and($consent->captured_ip)->not->toBeNull();
});

it('starts monthly-billed requests immediately', function () {
    $customerId = Fixtures::customer();
    $user = Fixtures::user($customerId);
    Fixtures::agreement($customerId, 'monthly');

    $this->actingAs($user, 'customer_user')
        ->post(route('client.request.submit'), submitPayload($this->scopeTypeId, $this->identityTypeId));

    // Post-pay customers have no payment gate.
    expect(ScreeningRequest::where('customer_id', $customerId)->value('status'))->toBe('in_progress');
});

it('holds cash-billed requests at new pending payment', function () {
    $customerId = Fixtures::customer();
    $user = Fixtures::user($customerId);
    Fixtures::agreement($customerId, 'per_request');

    $this->actingAs($user, 'customer_user')
        ->post(route('client.request.submit'), submitPayload($this->scopeTypeId, $this->identityTypeId))
        ->assertRedirect();

    expect(ScreeningRequest::where('customer_id', $customerId)->value('status'))->toBe('new');
});

it('refuses to submit without PDPA consent', function () {
    $customerId = Fixtures::customer();
    $user = Fixtures::user($customerId);
    Fixtures::agreement($customerId, 'monthly');

    $this->actingAs($user, 'customer_user')
        ->post(route('client.request.submit'), submitPayload($this->scopeTypeId, $this->identityTypeId, ['consent' => '']))
        ->assertSessionHasErrors('consent');

    expect(ScreeningRequest::where('customer_id', $customerId)->count())->toBe(0);
});

it('stamps the submitting user on the request', function () {
    $customerId = Fixtures::customer();
    $user = Fixtures::user($customerId);
    Fixtures::agreement($customerId, 'monthly');

    $this->actingAs($user, 'customer_user')
        ->post(route('client.request.submit'), submitPayload($this->scopeTypeId, $this->identityTypeId));

    expect(ScreeningRequest::where('customer_id', $customerId)->value('customer_user_id'))->toBe($user->id);
});

it('blocks a Viewer from reaching the create form', function () {
    $customerId = Fixtures::customer();
    $viewer = Fixtures::user($customerId, 'Viewer');
    Fixtures::agreement($customerId, 'monthly');

    $this->actingAs($viewer, 'customer_user')
        ->get(route('client.request.global'))
        ->assertForbidden();
});

it('blocks a Viewer from submitting a request', function () {
    $customerId = Fixtures::customer();
    $viewer = Fixtures::user($customerId, 'Viewer');
    Fixtures::agreement($customerId, 'monthly');

    $this->actingAs($viewer, 'customer_user')
        ->post(route('client.request.submit'), submitPayload($this->scopeTypeId, $this->identityTypeId))
        ->assertForbidden();

    expect(ScreeningRequest::where('customer_id', $customerId)->count())->toBe(0);
});

it('lets an HR role submit a request', function () {
    $customerId = Fixtures::customer();
    $hr = Fixtures::user($customerId, 'HR');
    Fixtures::agreement($customerId, 'monthly');

    $this->actingAs($hr, 'customer_user')
        ->post(route('client.request.submit'), submitPayload($this->scopeTypeId, $this->identityTypeId))
        ->assertRedirect(route('client.request.success'));

    expect(ScreeningRequest::where('customer_id', $customerId)->count())->toBe(1);
});
