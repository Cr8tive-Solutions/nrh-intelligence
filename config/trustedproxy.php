<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Trusted Proxies
    |--------------------------------------------------------------------------
    |
    | Only requests arriving from these addresses may set X-Forwarded-* headers.
    | Trusting '*' let any client that could reach the app spoof its IP with
    | X-Forwarded-For and so bypass the per-IP login throttles. Defaults to the
    | private ranges the reverse proxy reaches the container from (same as the
    | admin portal); override with a comma-separated TRUSTED_PROXIES list.
    |
    | Read by Illuminate\Http\Middleware\TrustProxies at request time, so it
    | honours .env and the config cache (bootstrap/app.php runs before .env is
    | loaded and so cannot read it).
    |
    */

    'proxies' => env('TRUSTED_PROXIES') ?: ['10.0.0.0/8', '172.16.0.0/12', '192.168.0.0/16'],

];
