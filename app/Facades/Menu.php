<?php

declare(strict_types=1);

namespace App\View\Components;

use Illuminate\Support\Facades\Facade;

final class Menu extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'menu';
    }
}
