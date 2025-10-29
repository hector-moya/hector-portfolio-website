<?php

namespace App\Providers;

use App\Services\NavigationService;
use Illuminate\Support\ServiceProvider;

class NavigationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('navigation', function ($app) {
            return new NavigationService();
        });
    }

    public function boot(): void
    {
        //
    }
}
