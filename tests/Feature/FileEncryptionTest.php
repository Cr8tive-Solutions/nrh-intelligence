<?php

use App\Models\ScreeningRequest;
use App\Support\EncryptedFilesystemAdapter;
use App\Support\Pii;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Tests\Support\Fixtures;

/**
 * Customer uploads are sealed at rest (EncryptedFilesystemAdapter on the
 * `local` disk) and opened again on the way out. The admin portal reads the
 * same files through its `client_local` mount with the same shared PII_KEY.
 */
beforeEach(function () {
    Fixtures::seedRoles();

    $this->root = storage_path('framework/testing/encrypted-'.uniqid());
    File::ensureDirectoryExists($this->root);

    // Storage::fake() would swap in the plain local driver and bypass the
    // encryption under test, so point the real disk at a throwaway root.
    config([
        'filesystems.disks.local.root' => $this->root,
        'pii.key' => 'base64:'.base64_encode(random_bytes(32)),
    ]);
    Storage::forgetDisk('local');
    Pii::flush();

    $this->customerId = Fixtures::customer();
    Fixtures::agreement($this->customerId, 'per_request');
    $this->owner = Fixtures::user($this->customerId, 'Owner');
    $this->requestId = Fixtures::screeningRequest($this->customerId, $this->owner->id, ['status' => 'new']);
});

afterEach(function () {
    config(['pii.key' => '']);
    Pii::flush();
    Storage::forgetDisk('local');
    File::deleteDirectory($this->root);
});

it('encrypts an uploaded payment slip at rest and serves it decrypted', function () {
    $pdf = "%PDF-1.4\nBank transfer slip RM 1,060.00\n%%EOF";
    $upload = UploadedFile::fake()->createWithContent('slip.pdf', $pdf);

    $this->actingAs($this->owner, 'customer_user')
        ->post(route('client.requests.payment-slip.store', hid($this->requestId)), ['payment_slip' => $upload])
        ->assertRedirect();

    $path = ScreeningRequest::find($this->requestId)->payment_slip_path;
    $raw = file_get_contents($this->root.'/'.$path);

    expect(EncryptedFilesystemAdapter::isEncrypted($raw))->toBeTrue()
        ->and($raw)->not->toContain('Bank transfer slip');

    $response = $this->actingAs($this->owner, 'customer_user')
        ->get(route('client.requests.payment-slip.download', hid($this->requestId)))
        ->assertOk();

    expect($response->streamedContent())->toBe($pdf);
});
