<?php

use App\Support\PortalGuide;
use Tests\Support\Fixtures;

/**
 * Mirrors the admin portal's tests/Feature/GuideTest.php.
 *
 * The two things that matter: the guide is behind authentication, and it is NOT
 * behind any permission — a guide only the Owner can read is worse than none.
 */
beforeEach(function () {
    $this->customerId = Fixtures::customer();
    Fixtures::agreement($this->customerId, 'monthly');
});

it('redirects guests to login', function () {
    $this->get(route('client.guide.show'))->assertRedirect(route('client.login'));
});

it('is readable by every role, not just the Owner', function (string $role) {
    Fixtures::seedRoles();

    $this->actingAs(Fixtures::user($this->customerId, $role), 'customer_user')
        ->get(route('client.guide.show'))
        ->assertOk()
        ->assertSee('User Guide')
        ->assertSee('Submitting a Screening Request');
})->with(['Owner', 'Admin', 'HR', 'Accounts']);

it('renders the guide as a PDF', function () {
    $response = $this->actingAs(Fixtures::user($this->customerId), 'customer_user')
        ->get(route('client.guide.download'));

    $response->assertOk()->assertHeader('Content-Type', 'application/pdf');

    // dompdf output, not an error page rendered with a PDF content type.
    // getContent(), not streamedContent(): download() returns a plain Response,
    // and streamedContent() hard-fails on anything that isn't streamed.
    expect(substr((string) $response->getContent(), 0, 5))->toBe('%PDF-');
});

it('gives every section a unique anchor so the contents links resolve', function () {
    $ids = array_column(PortalGuide::sections(), 'id');

    expect($ids)->not->toBeEmpty()
        ->and(array_unique($ids))->toHaveCount(count($ids))
        ->and(array_filter($ids, fn ($id) => $id === ''))->toBeEmpty();
});

it('only references routes that exist', function () {
    foreach (PortalGuide::sections() as $section) {
        foreach ($section['blocks'] as $block) {
            if (($block['type'] ?? null) !== 'links') {
                continue;
            }
            foreach ($block['items'] as $link) {
                expect(fn () => route($link['route']))->not->toThrow(Exception::class);
            }
        }
    }
});

it('only uses block types the renderer understands', function () {
    $known = ['p', 'sub', 'steps', 'list', 'table', 'note', 'warn', 'links'];

    foreach (PortalGuide::sections() as $section) {
        foreach ($section['blocks'] as $block) {
            expect($block['type'] ?? null)->toBeIn($known);
        }
    }
});

it('gives every table row the same column count as its header', function () {
    foreach (PortalGuide::sections() as $section) {
        foreach ($section['blocks'] as $block) {
            if (($block['type'] ?? null) !== 'table') {
                continue;
            }
            foreach ($block['rows'] as $row) {
                expect($row)->toHaveCount(count($block['head']));
            }
        }
    }
});
