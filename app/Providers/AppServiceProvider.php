<?php

declare(strict_types=1);

namespace App\Providers;

use App\View\Components\Menu;
use Flux\Flux;
use Illuminate\Support\ServiceProvider;
use Livewire\Blaze\Blaze;

final class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton('menu', Menu::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (! class_exists('flux', false)) {
            class_alias(Flux::class, 'flux');
        }

        Blaze::optimize()
            ->in(resource_path('views/components'))
            ->in(resource_path('views/components/layouts'), compile: false);
    }
}
