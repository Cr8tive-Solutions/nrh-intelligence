<?php

namespace App\Providers;

use Hashids\Hashids;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('hashids', fn () => new Hashids(config('app.key'), 8));
    }

    public function boot(): void
    {
        //
    }
}
