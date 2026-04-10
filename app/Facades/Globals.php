<?php

declare(strict_types=1);

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static mixed get(string $key = null, mixed $default = null)
 * @method static \Illuminate\Support\Collection all()
 * @method static void flush()
 */
class Globals extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'globals';
    }
}
