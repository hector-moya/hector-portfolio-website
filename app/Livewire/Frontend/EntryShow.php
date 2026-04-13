<?php

declare(strict_types=1);

namespace App\Livewire\Frontend;

use App\Models\Asset;
use App\Models\Collection as CollectionModel;
use App\Models\Entry;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Layout;
use Livewire\Component;

final class EntryShow extends Component
{
    public Entry $entry;

    public CollectionModel $collection;

    public function mount(string $collectionSlug, string $entrySlug): void
    {
        $this->collection = CollectionModel::query()
            ->where('slug', $collectionSlug)
            ->where('is_active', true)
            ->firstOrFail();

        $this->entry = Entry::query()
            ->where('slug', $entrySlug)
            ->where('blueprint_id', $this->collection->blueprint_id)
            ->where('status', 'published')
            ->with(['elements.Field', 'blueprint.tabs.sections.fields', 'author'])
            ->firstOrFail();
    }

    #[Layout('components.layouts.frontend')]
    public function render(): View|Factory
    {
        $sections = $this->entry->getPageBuilderSections();
        $assets = $this->resolveAssets($sections);
        $theme = $this->collection->settings['theme'] ?? 'greenpeace';

        return view('livewire.frontend.entry-show', [
            'sections' => $sections,
            'assets' => $assets,
            'theme' => $theme,
        ]);
    }

    public function title(): string
    {
        return $this->entry->title;
    }

    /**
     * Batch-load assets referenced by image fields in sections.
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
