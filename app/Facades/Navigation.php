<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static ?\App\Models\Navigation get(string $handle, bool $activeOnly = true)
 * @method static \Illuminate\Database\Eloquent\Collection all(bool $activeOnly = true)
 * @method static void reorder(array $items, ?int $parentId = null)
 * @method static void flush()
 * @method static \Illuminate\Support\Collection breadcrumb(\App\Models\NavigationItem $item)
 * @method static bool isActive(\App\Models\NavigationItem $item, string $url)
 */
class Navigation extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'navigation';
    }
}
