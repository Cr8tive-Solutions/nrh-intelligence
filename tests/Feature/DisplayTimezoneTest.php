<?php

use App\Models\Invoice;
use App\Models\ScreeningRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\Support\Fixtures;

/**
 * Timestamps are stored and computed in UTC and only converted to the display
 * timezone (Asia/Kuala_Lumpur, UTC+8) when shown, or when taking "today" for a
 * business date. Between 00:00 and 08:00 Malaysian time the UTC date is still
 * yesterday, so that window is where a missed conversion shows up.
 */
afterEach(function () {
    Carbon::setTestNow();
});

it('converts a UTC timestamp to Malaysian time without changing the original', function () {
    $stored = Carbon::parse('2026-11-05 21:54:00', 'UTC');

    $shown = $stored->inDisplayTz();

    expect($shown->format('Y-m-d H:i'))->toBe('2026-11-06 05:54')
        ->and($shown->getTimezone()->getName())->toBe('Asia/Kuala_Lumpur')
        ->and($stored->format('Y-m-d H:i e'))->toBe('2026-11-05 21:54 UTC');
});

it('keeps the app and database session on UTC', function () {
    expect(config('app.timezone'))->toBe('UTC')
        ->and(DB::selectOne('show timezone')->TimeZone)->toBe('UTC');
});

it('numbers references and invoices by the Malaysian year on New Year morning', function () {
    // 04:00 on 1 Jan 2027 in Malaysia, still 2026 in UTC.
    Carbon::setTestNow(Carbon::parse('2026-12-31 20:00:00', 'UTC'));

    [$reference, $number] = DB::transaction(fn () => [
        ScreeningRequest::generateReference(),
        Invoice::generateNumber(),
    ]);

    expect($reference)->toStartWith('REP-2027-')
        ->and($number)->toStartWith('INV-2027-');
});

it('filters the audit log by Malaysian calendar days', function () {
    Fixtures::seedRoles();
    $user = Fixtures::user(Fixtures::customer());

    $logAt = function (string $utc, string $description) use ($user) {
        $activity = activity('test')->causedBy($user)->log($description);
        $activity->forceFill(['created_at' => Carbon::parse($utc, 'UTC')])->save();
    };
    // 06 Nov 05:00 in Malaysia is 05 Nov 21:00 UTC — it belongs to 6 Nov.
    $logAt('2026-11-05 21:00:00', 'early on the 6th');
    // 05 Nov 23:00 in Malaysia.
    $logAt('2026-11-05 15:00:00', 'late on the 5th');

    $this->actingAs($user, 'customer_user')
        ->get(route('client.settings.audit-log', ['from' => '2026-11-06', 'to' => '2026-11-06']))
        ->assertOk()
        ->assertViewHas('activities', fn ($activities) => collect($activities->items())
            ->pluck('description')->all() === ['early on the 6th']);
});

it('accepts a payment receipt paid on the Malaysian today before 08:00', function () {
    // 06:00 on 6 Nov in Malaysia, still 5 Nov in UTC.
    Carbon::setTestNow(Carbon::parse('2026-11-05 22:00:00', 'UTC'));
    Storage::fake('local');
    Fixtures::seedRoles();
    $customerId = Fixtures::customer();
    $user = Fixtures::user($customerId);
    $invoice = Invoice::find(Fixtures::invoice($customerId));

    $this->actingAs($user, 'customer_user')
        ->withSession(['client_customer_id' => $customerId, 'client_user_id' => $user->id])
        ->post(route('client.billing.invoices.receipts.store', hid($invoice->id)), [
            'receipt_file' => UploadedFile::fake()->create('receipt.pdf', 20, 'application/pdf'),
            'paid_on' => '2026-11-06',
        ])
        ->assertSessionHasNoErrors();

    expect($invoice->receipts()->first()?->paid_on->toDateString())->toBe('2026-11-06');
});

it('converts every stored timestamp a view formats', function () {
    $unconverted = [];

    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator(resource_path('views'))) as $file) {
        if (! str_ends_with($file->getFilename(), '.blade.php')) {
            continue;
        }

        foreach (file($file->getPathname()) as $index => $line) {
            // A *_at attribute or now() formatted directly, without ->inDisplayTz() first.
            if (preg_match('/(\w_at\)?|\bnow\(\))\??->(?!inDisplayTz\()(?:\w+\([^)]*\)->)*(format|isoFormat|toDateString)\(/', $line)) {
                $unconverted[] = str_replace(resource_path('views').'/', '', $file->getPathname()).':'.($index + 1);
            }
        }
    }

    expect($unconverted)->toBe([]);
});
