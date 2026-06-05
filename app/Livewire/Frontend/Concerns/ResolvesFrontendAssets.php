<?php

declare(strict_types=1);

namespace App\Livewire\Frontend\Concerns;

use App\Models\Asset;
use Illuminate\Database\Eloquent\Collection;

trait ResolvesFrontendAssets
{
    /**
     * Batch-load assets referenced by image fields in page builder sections,
     * avoiding N+1 lookups when sections render.
     *
     * @param  array<int, array{type: string, data: array<string, mixed>}>  $sections
     * @return Collection<int, Asset>
     */
    protected function resolveAssets(array $sections): Collection
    {
        if ($sections === []) {
            return new Collection;
        }

        $assetIds = collect($sections)
            ->flatMap(fn (array $section): array => match ($section['type']) {
                'hero' => [$section['data']['bg_image'] ?? null],
                'image_text' => [$section['data']['image'] ?? null],
                'gallery' => $section['data']['images'] ?? [],
                'card_grid' => collect($section['data']['cards'] ?? [])->pluck('image')->filter()->all(),
                default => [],
            })
            ->filter()
            ->unique()
            ->values()
            ->all();

        if ($assetIds === []) {
            return new Collection;
        }

        return Asset::query()->whereIn('id', $assetIds)->get();
    }
}
