<?php

declare(strict_types=1);

namespace App\Facades;

use Illuminate\Database\Eloquent\Collection;
use App\Models\NavigationItem;
use Illuminate\Support\Facades\Facade;

/**
 * @method static ?\App\Models\Navigation get(string $handle, bool $activeOnly = true)
 * @method static Collection all(bool $activeOnly = true)
 * @method static void reorder(array $items, ?int $parentId = null)
 * @method static void flush()
 * @method static \Illuminate\Support\Collection breadcrumb(NavigationItem $item)
 * @method static bool isActive(NavigationItem $item, string $url)
 */
final class Navigation extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'navigation';
    }
}
