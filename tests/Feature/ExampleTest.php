<?php

// The client portal has no public landing page for guests — "/" bounces to login.
test('an unauthenticated visitor is redirected away from the root', function () {
    $this->get('/')->assertRedirect();
});
