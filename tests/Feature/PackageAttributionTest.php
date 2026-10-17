<?php

use App\Models\ScreeningRequest;
use Illuminate\Support\Facades\DB;
use Tests\Support\Fixtures;

/**
 * `screening_requests.package_id` records which package a request was ordered
 * from. The posted id is only a hint — submit() re-derives it, attributing the
 * request only when the ordered scopes are exactly the package's scopes.
 */
beforeEach(function () {
    $this->countryId = Fixtures::country();
    $this->identityTypeId = Fixtures::identityType();
    $this->scopeA = Fixtures::scopeType($this->countryId);
    $this->scopeB = Fixtures::scopeType($this->countryId);

    $this->customerId = Fixtures::customer();
    $this->user = Fixtures::user($this->customerId);
    Fixtures::agreement($this->customerId, 'monthly');
});

/**
 * Submission payload with an explicit cart and optional package hint.
 *
 * @param  list<int>  $scopeIds
 */
function attributionPayload(array $scopeIds, int $identityTypeId, ?int $packageId = null): array
{
    return [
        'screening_type' => 'employment_global',
        'consent' => '1',
        'cart_data' => json_encode(array_map(fn ($id) => ['id' => $id], $scopeIds)),
        'candidates_data' => json_encode([[
            'identity_type_id' => $identityTypeId,
            'name' => 'Ali bin Abu',
            'identity_number' => '900101-01-1234',
            'nationality' => 'Malaysian',
        ]]),
        'package_id' => $packageId,
    ];
}

it('attributes the request when the cart matches the package exactly', function () {
    $packageId = Fixtures::package($this->customerId, $this->countryId);
    Fixtures::attachPackageScope($packageId, $this->scopeA);
    Fixtures::attachPackageScope($packageId, $this->scopeB);

    $this->actingAs($this->user, 'customer_user')
        ->post(route('client.request.submit'), attributionPayload([$this->scopeA, $this->scopeB], $this->identityTypeId, $packageId))
        ->assertRedirect(route('client.request.success'));

    expect(ScreeningRequest::where('customer_id', $this->customerId)->value('package_id'))->toBe($packageId);
});

it('ignores cart ordering when matching the package', function () {
    $packageId = Fixtures::package($this->customerId, $this->countryId);
    Fixtures::attachPackageScope($packageId, $this->scopeA);
    Fixtures::attachPackageScope($packageId, $this->scopeB);

    // Reversed order — still the same set.
    $this->actingAs($this->user, 'customer_user')
        ->post(route('client.request.submit'), attributionPayload([$this->scopeB, $this->scopeA], $this->identityTypeId, $packageId));

    expect(ScreeningRequest::where('customer_id', $this->customerId)->value('package_id'))->toBe($packageId);
});

it('stores no package for an ad-hoc selection', function () {
    $this->actingAs($this->user, 'customer_user')
        ->post(route('client.request.submit'), attributionPayload([$this->scopeA], $this->identityTypeId))
        ->assertRedirect(route('client.request.success'));

    expect(ScreeningRequest::where('customer_id', $this->customerId)->value('package_id'))->toBeNull();
});

it('drops the attribution when a scope was removed from the cart', function () {
    $packageId = Fixtures::package($this->customerId, $this->countryId);
    Fixtures::attachPackageScope($packageId, $this->scopeA);
    Fixtures::attachPackageScope($packageId, $this->scopeB);

    // Package claims two scopes, only one was ordered.
    $this->actingAs($this->user, 'customer_user')
        ->post(route('client.request.submit'), attributionPayload([$this->scopeA], $this->identityTypeId, $packageId));

    expect(ScreeningRequest::where('customer_id', $this->customerId)->value('package_id'))->toBeNull();
});

it('drops the attribution when an extra scope was added to the cart', function () {
    $packageId = Fixtures::package($this->customerId, $this->countryId);
    Fixtures::attachPackageScope($packageId, $this->scopeA);

    $this->actingAs($this->user, 'customer_user')
        ->post(route('client.request.submit'), attributionPayload([$this->scopeA, $this->scopeB], $this->identityTypeId, $packageId));

    expect(ScreeningRequest::where('customer_id', $this->customerId)->value('package_id'))->toBeNull();
});

it('refuses a package belonging to another customer', function () {
    $otherId = Fixtures::customer();
    $foreignPackage = Fixtures::package($otherId, $this->countryId);
    Fixtures::attachPackageScope($foreignPackage, $this->scopeA);

    $this->actingAs($this->user, 'customer_user')
        ->post(route('client.request.submit'), attributionPayload([$this->scopeA], $this->identityTypeId, $foreignPackage));

    expect(ScreeningRequest::where('customer_id', $this->customerId)->value('package_id'))->toBeNull();
});

it('ignores a non-existent or non-numeric package id', function (mixed $posted) {
    $this->actingAs($this->user, 'customer_user')
        ->post(route('client.request.submit'), attributionPayload([$this->scopeA], $this->identityTypeId) + ['package_id' => $posted]);

    expect(ScreeningRequest::where('customer_id', $this->customerId)->value('package_id'))->toBeNull();
})->with([
    'missing id' => [999999],
    'not numeric' => ['abc'],
    'empty string' => [''],
]);

it('nulls the attribution when the package is deleted, keeping the request', function () {
    $packageId = Fixtures::package($this->customerId, $this->countryId);
    Fixtures::attachPackageScope($packageId, $this->scopeA);

    $this->actingAs($this->user, 'customer_user')
        ->post(route('client.request.submit'), attributionPayload([$this->scopeA], $this->identityTypeId, $packageId));

    $requestId = ScreeningRequest::where('customer_id', $this->customerId)->value('id');
    expect(ScreeningRequest::find($requestId)->package_id)->toBe($packageId);

    // nullOnDelete, not cascade — losing a package must never delete requests.
    DB::table('package_scope_type')->where('package_id', $packageId)->delete();
    DB::table('packages')->where('id', $packageId)->delete();

    $request = ScreeningRequest::find($requestId);
    expect($request)->not->toBeNull()
        ->and($request->package_id)->toBeNull();
});

it('exposes the package through the relation', function () {
    $packageId = Fixtures::package($this->customerId, $this->countryId, ['name' => 'Finance Sector Pack']);
    Fixtures::attachPackageScope($packageId, $this->scopeA);

    $this->actingAs($this->user, 'customer_user')
        ->post(route('client.request.submit'), attributionPayload([$this->scopeA], $this->identityTypeId, $packageId));

    $request = ScreeningRequest::with('package')->where('customer_id', $this->customerId)->firstOrFail();

    expect($request->package->name)->toBe('Finance Sector Pack');
});
