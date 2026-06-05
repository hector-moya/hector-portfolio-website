<?php

declare(strict_types=1);

namespace App\Livewire\Frontend;

use App\Livewire\Frontend\Concerns\ResolvesFrontendAssets;
use App\Models\Collection as CollectionModel;
use App\Models\Entry;
use App\Support\Seo;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

final class EntryShow extends Component
{
    use ResolvesFrontendAssets;

    public Entry $entry;

    public CollectionModel $collection;

    public function mount(string $collectionSlug, string $entrySlug): void
    {
        $parent = CollectionModel::query()
            ->where('slug', $collectionSlug)
            ->where('is_active', true)
            ->firstOrFail();

        if (($parent->settings['type'] ?? 'standard') === 'main') {
            // Entry lives in one of this collection's children
            $blueprintIds = $parent->children()
                ->where('is_active', true)
                ->pluck('blueprint_id')
                ->filter();

            $this->entry = Entry::query()
                ->where('slug', $entrySlug)
                ->whereIn('blueprint_id', $blueprintIds)
                ->where('status', 'published')
                ->with(['elements.field', 'blueprint.tabs.sections.fields', 'author'])
                ->firstOrFail();

            $this->collection = $parent->children()
                ->where('blueprint_id', $this->entry->blueprint_id)
                ->firstOrFail();
        } else {
            $this->collection = $parent;
            $this->entry = Entry::query()
                ->where('slug', $entrySlug)
                ->where('blueprint_id', $this->collection->blueprint_id)
                ->where('status', 'published')
                ->with(['elements.field', 'blueprint.tabs.sections.fields', 'author'])
                ->firstOrFail();
        }
    }

    #[Layout('components.layouts.frontend')]
    public function render(): View|Factory
    {
        $sections = $this->entry->getPageBuilderSections();
        $assets = $this->resolveAssets($sections);
        $theme = $this->collection->settings['theme'] ?? 'greenpeace';

        $seo = Seo::forEntry($this->entry, ['ogType' => 'article']);

        return view('livewire.frontend.entry-show', [
            'sections' => $sections,
            'assets' => $assets,
            'theme' => $theme,
        ])
            ->title($seo['title'])
            ->layoutData([
                'description' => $seo['description'],
                'ogImage' => $seo['ogImage'],
                'ogType' => $seo['ogType'],
            ]);
    }
}
