<?php

namespace App\Providers;

use App\View\Components\Menu;
use Flux\Flux;
use Illuminate\Support\ServiceProvider;
use Livewire\Blaze\Blaze;

class AppServiceProvider extends ServiceProvider
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
        class_alias(Flux::class, 'flux');

        Blaze::optimize()
            ->in(resource_path('views/components'))
            ->in(resource_path('views/components/layouts'), compile: false);
    }
}
