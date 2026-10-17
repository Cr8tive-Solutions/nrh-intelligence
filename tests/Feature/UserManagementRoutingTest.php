<?php

use App\Models\CustomerUser;
use Tests\Support\Fixtures;

/**
 * Regression cover for "Failed to load user" on Settings → Users → Edit.
 *
 * CustomerUser is hashid-bound, so every /settings/users/{user} URL must carry
 * a hashid. The Edit button was dispatching the raw integer id, the fetch 404'd,
 * and the modal surfaced the generic failure. Update and delete built their URLs
 * from the same raw id and were broken the same way.
 */
beforeEach(function () {
    Fixtures::seedRoles();

    $this->customerId = Fixtures::customer();
    $this->owner = Fixtures::user($this->customerId, 'Owner');
    $this->member = Fixtures::user($this->customerId, 'HR');
});

it('returns the user as json for a hashid', function () {
    $this->actingAs($this->owner, 'customer_user')
        ->getJson('/settings/users/'.hid($this->member->id).'/edit')
        ->assertOk()
        ->assertJsonPath('name', $this->member->name)
        ->assertJsonPath('role', 'HR')
        ->assertJsonStructure(['hashid', 'id', 'name', 'email', 'status', 'role', 'role_permissions', 'direct_permissions', 'all_permissions']);
});

it('exposes a hashid the modal can build urls from', function () {
    $this->actingAs($this->owner, 'customer_user')
        ->getJson('/settings/users/'.hid($this->member->id).'/edit')
        ->assertOk()
        ->assertJsonPath('hashid', hid($this->member->id));
});

it('404s on a raw integer id', function () {
    // The exact shape of the original bug: a raw id is not a decodable hashid.
    $this->actingAs($this->owner, 'customer_user')
        ->getJson('/settings/users/'.$this->member->id.'/edit')
        ->assertNotFound();
});

it('renders the edit button with a hashid, not a raw id', function () {
    $response = $this->actingAs($this->owner, 'customer_user')
        ->get(route('client.settings.users'))
        ->assertOk();

    $response->assertSee("open-edit-user', { id: '".hid($this->member->id)."' }", false)
        ->assertDontSee("open-edit-user', { id: ".$this->member->id.' }', false);
});

it('updates a user addressed by hashid', function () {
    $this->actingAs($this->owner, 'customer_user')
        ->put('/settings/users/'.hid($this->member->id), [
            'name' => 'Renamed Person',
            'email' => $this->member->email,
            'role' => 'Viewer',
            'status' => 'active',
        ])
        ->assertRedirect();

    $fresh = CustomerUser::findOrFail($this->member->id);

    expect($fresh->name)->toBe('Renamed Person')
        ->and($fresh->roles->first()?->name)->toBe('Viewer');
});

it('deletes a user addressed by hashid', function () {
    $this->actingAs($this->owner, 'customer_user')
        ->delete('/settings/users/'.hid($this->member->id))
        ->assertRedirect();

    expect(CustomerUser::find($this->member->id))->toBeNull();
});

it('does not expose a user from another customer', function () {
    $otherCustomerId = Fixtures::customer();
    $foreign = Fixtures::user($otherCustomerId, 'Owner');

    $this->actingAs($this->owner, 'customer_user')
        ->getJson('/settings/users/'.hid($foreign->id).'/edit')
        ->assertNotFound();
});

it('blocks a role without manage-team', function () {
    $hr = Fixtures::user($this->customerId, 'HR');

    $this->actingAs($hr, 'customer_user')
        ->getJson('/settings/users/'.hid($this->member->id).'/edit')
        ->assertForbidden();
});
