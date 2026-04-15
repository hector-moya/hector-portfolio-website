<?php

declare(strict_types=1);

namespace App\Livewire\Frontend;

use App\Models\Asset;
use App\Models\Entry;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

final class Home extends Component
{
    #[Layout('components.layouts.frontend')]
    #[Title('Home')]
    public function render(): View|Factory
    {
        $entry = Entry::query()
            ->where('slug', 'home')
            ->where('status', 'published')
            ->with(['elements.field', 'collection'])
            ->first();

        $sections = $entry?->getPageBuilderSections() ?? [];
        $assets = $this->resolveAssets($sections);
        $theme = $entry?->collection?->settings['theme'] ?? 'greenpeace';

        return view('livewire.frontend.home', [
            'entry' => $entry,
            'layout' => $sections,
            'assets' => $assets,
            'theme' => $theme,
        ]);
    }

    /**
     * Batch-load assets referenced by image fields in page builder sections.
     */
    private function resolveAssets(array $sections): Collection
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

        if (empty($assetIds)) {
            return new Collection;
        }

        return Asset::query()->whereIn('id', $assetIds)->get();
    }
}
