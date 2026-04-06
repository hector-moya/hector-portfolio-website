<?php

namespace App\Livewire\Frontend;

use App\Models\Collection as CollectionModel;
use App\Support\TemplateLayouts;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class CollectionIndex extends Component
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
        $entries = $this->collection->entries()
            ->where('status', 'published')
            ->with(['elements.Field', 'author'])
            ->latest('published_at')
            ->paginate(9);

        $template = $this->collection->settings['index_template']
            ?? TemplateLayouts::defaultIndexTemplate();

        return view('livewire.frontend.collection-index', [
            'entries' => $entries,
            'template' => $template,
            'theme' => $this->collection->settings['theme'] ?? 'greenpeace',
        ]);
    }

    public function title(): string
    {
        return $this->collection->name;
    }
}
