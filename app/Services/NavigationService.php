<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Navigation;
use App\Models\NavigationItem;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\Cache;

final class NavigationService
{
    /**
     * Get a navigation menu by its handle.
     */
    public function get(string $handle, bool $activeOnly = true): ?Navigation
    {
        return Cache::rememberForever('navigation.' . $handle, function () use ($handle, $activeOnly) {
            $query = Navigation::with(['items' => function ($query) use ($activeOnly): void {
                $query->with('children')->orderBy('order');

                if ($activeOnly) {
                    $query->where('is_active', true);
                }
            }]);

            if ($activeOnly) {
                $query->where('is_active', true);
            }

            return $query->where('handle', $handle)->first();
        });
    }

    /**
     * Get all navigation menus.
     */
    public function all(bool $activeOnly = true): Collection
    {
        $cacheKey = $activeOnly ? 'navigation.all.active' : 'navigation.all';

        return Cache::rememberForever($cacheKey, function () use ($activeOnly) {
            $query = Navigation::with(['items' => function ($query) use ($activeOnly): void {
                $query->with('children')->orderBy('order');

                if ($activeOnly) {
                    $query->where('is_active', true);
                }
            }]);

            if ($activeOnly) {
                $query->where('is_active', true);
            }

            return $query->get();
        });
    }

    /**
     * Reorder navigation items.
     */
    public function reorder(array $items, ?int $parentId = null): void
    {
        collect($items)->each(function (array $item, $index) use ($parentId): void {
            NavigationItem::query()->where('id', $item['id'])->update([
                'parent_id' => $parentId,
                'order' => $index,
            ]);

            if (isset($item['children']) && count($item['children'])) {
                $this->reorder($item['children'], $item['id']);
            }
        });

        $this->flush();
    }

    /**
     * Flush all navigation caches.
     */
    public function flush(): void
    {
        Cache::forget('navigation.all');
        Cache::forget('navigation.all.active');

        Navigation::query()->pluck('handle')->each(function (string $handle): void {
            Cache::forget('navigation.' . $handle);
        });
    }

    /**
     * Get the breadcrumb trail for a navigation item.
     */
    public function breadcrumb(NavigationItem $item): SupportCollection
    {
        $trail = collect([$item]);
        $parent = $item->parent;

        while ($parent) {
            $trail->prepend($parent);
            $parent = $parent->parent;
        }

        return $trail;
    }

    /**
     * Check if a URL matches a navigation item's URL.
     */
    /**
     * Check if a URL matches a navigation item's URL.
     */
    public function isActive(NavigationItem $item, string $url): bool
    {
        if ($item->url === $url) {
            return true;
        }

        return $item->children->contains(fn (NavigationItem $child): bool => $this->isActive($child, $url));
    }
}
