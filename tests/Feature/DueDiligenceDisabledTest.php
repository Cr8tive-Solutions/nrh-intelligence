<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Tests\Support\Fixtures;

/**
 * Due Diligence (KYC / KYB / KYS) is parked pending client confirmation.
 *
 * Hiding the sidebar links was not enough — the endpoints stayed reachable by
 * direct URL, and a submitted due-diligence request creates a ScreeningRequest
 * with NO candidate_scope_type rows: it prices at RM0 and the admin portal has
 * no way to record findings or generate a report for it. The routes are
 * therefore commented out in routes/client.php.
 *
 * If these tests fail, someone re-enabled the feature — make sure the admin
 * side can actually process it first.
 */
beforeEach(function () {
    $this->customerId = Fixtures::customer();
    $this->user = Fixtures::user($this->customerId);
    Fixtures::agreement($this->customerId, 'monthly');
});

it('has no registered route for the due diligence endpoints', function (string $method, string $uri) {
    $matched = true;
    try {
        Route::getRoutes()->match(Request::create('/'.$uri, $method));
    } catch (Throwable) {
        $matched = false;
    }

    expect($matched)->toBeFalse();
})->with([
    ['GET', 'request/kyc'],
    ['GET', 'request/kyb'],
    ['GET', 'request/kys'],
    ['POST', 'request/due-diligence/submit'],
]);

it('404s an authenticated user hitting a due diligence url directly', function (string $uri) {
    $this->actingAs($this->user, 'customer_user')->get('/'.$uri)->assertNotFound();
})->with(['request/kyc', 'request/kyb', 'request/kys']);

it('cannot submit a due diligence request by posting directly', function () {
    $this->actingAs($this->user, 'customer_user')
        ->post('/request/due-diligence/submit', [
            'screening_type' => 'kyc',
            'consent' => '1',
            'subject_data' => json_encode(['name' => 'Bypass Attempt', 'identity_number' => '123']),
            'checks_data' => json_encode([['id' => 'identity']]),
        ])
        ->assertNotFound();

    // Nothing was created — this is the outcome that actually matters.
    expect(DB::table('screening_requests')->where('customer_id', $this->customerId)->count())->toBe(0);
});

it('keeps the employment request routes working', function () {
    $this->actingAs($this->user, 'customer_user')->get(route('client.request.global'))->assertOk();
    $this->actingAs($this->user, 'customer_user')->get(route('client.request.malaysia'))->assertOk();
});
