<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\NavigationService;
use Illuminate\Support\ServiceProvider;

final class NavigationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('navigation', fn ($app): NavigationService => new NavigationService);
    }

    public function boot(): void
    {
        //
    }
}
