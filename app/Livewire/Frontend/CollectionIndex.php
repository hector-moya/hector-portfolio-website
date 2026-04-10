<?php

declare(strict_types=1);

namespace App\Livewire\Frontend;

use App\Models\Asset;
use App\Models\Collection as CollectionModel;
use App\Support\TemplateLayouts;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

final class CollectionIndex extends Component
{
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
        if (($this->collection->settings['type'] ?? 'standard') === 'single') {
            return $this->renderSingle();
        }

        $entries = $this->collection->entries()
            ->where('status', 'published')
            ->with(['elements.Field', 'author'])
            ->latest('published_at')
            ->paginate(9);

        $template = $this->collection->settings['index_template']
            ?? TemplateLayouts::defaultIndexTemplate();

        if (! array_key_exists($template, TemplateLayouts::indexTemplates())) {
            $template = TemplateLayouts::defaultIndexTemplate();
        }

        return view('livewire.frontend.collection-index', [
            'entries' => $entries,
            'template' => $template,
            'theme' => $this->collection->settings['theme'] ?? 'greenpeace',
            'isSingle' => false,
        ]);
    }

    public function title(): string
    {
        return $this->collection->name;
    }

    private function renderSingle(): View|Factory
    {
        $entry = $this->collection->entries()
            ->where('status', 'published')
            ->with(['elements.Field', 'blueprint'])
            ->latest('published_at')
            ->first();

        $sections = $entry?->getPageBuilderSections() ?? [];
        $assets = $this->resolveAssets($sections);
        $theme = $this->collection->settings['theme'] ?? 'greenpeace';

        return view('livewire.frontend.collection-index', [
            'entry' => $entry,
            'sections' => $sections,
            'assets' => $assets,
            'template' => 'landing-page',
            'theme' => $theme,
            'isSingle' => true,
        ]);
    }

    /**
     * Batch-load assets referenced by image fields in page builder sections.
     */
    private function resolveAssets(array $sections): EloquentCollection
    {
        if ($sections === []) {
            return new EloquentCollection;
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
            return new EloquentCollection;
        }

        return Asset::query()->whereIn('id', $assetIds)->get();
    }
}
