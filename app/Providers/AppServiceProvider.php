<?php

namespace App\Providers;

use Hashids\Hashids;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Salt falls back to APP_KEY for existing URLs; set HASHIDS_SALT to
        // decouple URL ids from key rotation (see config/hashids.php).
        $this->app->singleton('hashids', fn () => new Hashids(
            config('hashids.salt') ?: config('app.key'),
            (int) config('hashids.min_length', 8),
        ));
    }

    public function boot(): void
    {
        //
    }
}
