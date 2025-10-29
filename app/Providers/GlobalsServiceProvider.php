<?php

namespace App\Providers;

use App\Services\GlobalsService;
use Illuminate\Support\ServiceProvider;

class GlobalsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('globals', function ($app) {
            return new GlobalsService;
        });
    }

    public function boot(): void
    {
        //
    }
}
