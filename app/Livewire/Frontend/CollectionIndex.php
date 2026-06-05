<?php

declare(strict_types=1);

namespace App\Livewire\Frontend;

use App\Livewire\Frontend\Concerns\ResolvesFrontendAssets;
use App\Models\Collection as CollectionModel;
use App\Models\Entry;
use App\Support\Seo;
use App\Support\TemplateLayouts; // Used for index templates (card-grid, list, magazine)
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

final class CollectionIndex extends Component
{
    use ResolvesFrontendAssets;
    use WithPagination;

    public CollectionModel $collection;

    public function mount(string $collectionSlug): void
    {
        $this->collection = CollectionModel::query()
            ->where('slug', $collectionSlug)
            ->where('is_active', true)
            ->firstOrFail();
    }

    #[Layout('components.layouts.frontend')]
    public function render(): View|Factory
    {
        $type = $this->collection->settings['type'] ?? 'standard';

        if ($type === 'single') {
            return $this->renderSingle();
        }

        if ($type === 'main') {
            return $this->renderMain();
        }

        $entries = $this->collection->entries()
            ->where('status', 'published')
            ->with(['elements.field', 'author'])
            ->latest('published_at')
            ->paginate(9);

        $template = $this->collection->settings['index_template']
            ?? TemplateLayouts::defaultIndexTemplate();

        if (! array_key_exists($template, TemplateLayouts::indexTemplates())) {
            $template = TemplateLayouts::defaultIndexTemplate();
        }

        return $this->withSeo(view('livewire.frontend.collection-index', [
            'entries' => $entries,
            'template' => $template,
            'theme' => $this->collection->settings['theme'] ?? 'greenpeace',
            'isSingle' => false,
        ]));
    }

    private function renderSingle(): View|Factory
    {
        $entry = $this->collection->entries()
            ->where('status', 'published')
            ->with(['elements.field', 'blueprint'])
            ->latest('published_at')
            ->first();

        $sections = $entry?->getPageBuilderSections() ?? [];
        $assets = $this->resolveAssets($sections);
        $theme = $this->collection->settings['theme'] ?? 'greenpeace';

        return $this->withSeo(view('livewire.frontend.collection-index', [
            'entry' => $entry,
            'sections' => $sections,
            'assets' => $assets,
            'template' => 'landing-page',
            'theme' => $theme,
            'isSingle' => true,
        ]), $entry);
    }

    private function renderMain(): View|Factory
    {
        $entry = $this->collection->entries()
            ->where('status', 'published')
            ->with(['elements.field', 'blueprint'])
            ->latest('published_at')
            ->first();

        $sections = $entry?->getPageBuilderSections() ?? [];
        $assets = $this->resolveAssets($sections);
        $theme = $this->collection->settings['theme'] ?? 'greenpeace';

        $childTemplate = $this->collection->settings['index_template']
            ?? TemplateLayouts::defaultIndexTemplate();

        if (! array_key_exists($childTemplate, TemplateLayouts::indexTemplates())) {
            $childTemplate = TemplateLayouts::defaultIndexTemplate();
        }

        $childEntries = $this->collection->children()
            ->where('is_active', true)
            ->with(['entries' => fn ($q) => $q->where('status', 'published')->with(['elements.field', 'author'])->latest('published_at')])
            ->get()
            ->flatMap(fn (CollectionModel $child) => $child->entries)
            ->sortByDesc('published_at')
            ->values();

        return $this->withSeo(view('livewire.frontend.collection-index', [
            'entry' => $entry,
            'sections' => $sections,
            'assets' => $assets,
            'template' => 'main',
            'childTemplate' => $childTemplate,
            'childEntries' => $childEntries,
            'collection' => $this->collection,
            'theme' => $theme,
            'isSingle' => false,
        ]), $entry);
    }

    /**
     * Apply SEO metadata to a view, preferring the resolved entry's fields
     * and falling back to the collection name.
     */
    private function withSeo(View $view, ?Entry $entry = null): View
    {
        $seo = Seo::forEntry($entry, ['title' => $this->collection->name]);

        return $view
            ->title($seo['title'])
            ->layoutData([
                'description' => $seo['description'],
                'ogImage' => $seo['ogImage'],
                'ogType' => $seo['ogType'],
            ]);
    }
}
