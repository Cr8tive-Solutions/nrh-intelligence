<?php

it('shows the terms of use without signing in', function () {
    $this->get(route('client.terms'))
        ->assertOk()
        ->assertSee('Terms of <em>Use</em>', false)
        ->assertSee(config('app.company.reg_no'))
        ->assertSee(route('client.privacy'));
});

it('shows the privacy notice without signing in', function () {
    $this->get(route('client.privacy'))
        ->assertOk()
        ->assertSee('Privacy <em>Notice</em>', false)
        ->assertSee('Personal Data Protection Act 2010')
        ->assertSee(config('app.support_email'));
});

it('links the sign-in page to the portal legal pages', function () {
    $this->get(route('client.login'))
        ->assertOk()
        ->assertSee('href="/terms"', false)
        ->assertSee('href="/privacy"', false);
});
