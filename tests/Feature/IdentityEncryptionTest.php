<?php

use App\Models\RequestCandidate;
use App\Support\Pii;
use Illuminate\Support\Facades\DB;
use Tests\Support\Fixtures;

beforeEach(function () {
    $this->countryId = Fixtures::country();
    $this->identityTypeId = Fixtures::identityType();
    $this->customerId = Fixtures::customer();
    $this->user = Fixtures::user($this->customerId);
    $this->requestId = Fixtures::screeningRequest($this->customerId, $this->user->id);
    Pii::flush();
});

afterEach(function () {
    config(['pii.key' => '']);
    Pii::flush();
});

function enableClientPii(): void
{
    config(['pii.key' => 'base64:'.base64_encode(random_bytes(32))]);
    Pii::flush();
}

it('encrypts identity_number and indexes it when a candidate is created client-side', function () {
    enableClientPii();

    $candidate = RequestCandidate::create([
        'screening_request_id' => $this->requestId,
        'identity_type_id' => $this->identityTypeId,
        'name' => 'Ali bin Abu',
        'identity_number' => '880101-14-5678',
        'status' => 'new',
    ]);

    expect(DB::table('request_candidates')->where('id', $candidate->id)->value('identity_number'))
        ->not->toBe('880101-14-5678');
    expect(RequestCandidate::find($candidate->id)->identity_number)->toBe('880101-14-5678')
        ->and($candidate->identity_number_hash)->toBe(Pii::hash('880101-14-5678'));
});

it('finds a candidate by exact IC through the track search when encrypted', function () {
    enableClientPii();

    RequestCandidate::create([
        'screening_request_id' => $this->requestId,
        'identity_type_id' => $this->identityTypeId,
        'name' => 'Ali bin Abu',
        'identity_number' => '880101-14-5678',
        'status' => 'new',
    ]);

    $this->actingAs($this->user, 'customer_user');
    session(['client_customer_id' => $this->customerId]);

    // Exact IC matches via the blind index...
    $this->post(route('client.requests.track.search'), ['q' => '880101-14-5678'])
        ->assertOk()
        ->assertSee('Ali bin Abu');

    // ...a non-matching IC does not.
    $this->post(route('client.requests.track.search'), ['q' => '000000-00-0000'])
        ->assertOk()
        ->assertDontSee('Ali bin Abu');
});
