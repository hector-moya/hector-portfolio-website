<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Entry;
use Illuminate\Support\Collection;

final class AssetUsage
{
    /**
     * Return all entries that reference the given asset ID.
     *
     * Scans both EntryElement.value (for image/file fields) and
     * Entry.layout JSON (for page builder sections).
     *
     * @return Collection<int, Entry>
     */
    public function usages(int $assetId): Collection
    {
        $assetIdString = (string) $assetId;

        // Entries via field values
        $viaElements = Entry::query()
            ->whereHas('elements', function ($query) use ($assetIdString): void {
                $query->where('value', $assetIdString);
            })
            ->with('blueprint')
            ->get();

        // Entries via page builder layout JSON
        $viaLayout = Entry::query()
            ->whereNotNull('layout')
            ->get()
            ->filter(fn (Entry $entry): bool => $this->layoutContainsAsset($entry->layout ?? [], $assetIdString));

        return $viaElements->merge($viaLayout)->unique('id')->values();
    }

    public function count(int $assetId): int
    {
        return $this->usages($assetId)->count();
    }

    /**
     * Recursively check whether a layout array contains the given asset ID value.
     *
     * @param  array<array-key, mixed>  $layout
     */
    private function layoutContainsAsset(array $layout, string $assetId): bool
    {
        array_walk_recursive($layout, function (mixed $value) use ($assetId, &$found): void {
            if ((string) $value === $assetId) {
                $found = true;
            }
        });

        return $found ?? false;
    }
}
