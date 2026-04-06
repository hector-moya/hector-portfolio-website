<?php

namespace App\Livewire\Frontend;

use App\Models\Collection as CollectionModel;
use App\Models\Entry;
use App\Support\TemplateLayouts;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

class EntryShow extends Component
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
            ->with(['elements.Field', 'blueprint.tabs.sections.fields.children', 'author'])
            ->firstOrFail();
    }

    #[Layout('components.layouts.frontend')]
    public function render(): View|Factory
    {
        $template = $this->entry->blueprint->settings['detail_template']
            ?? TemplateLayouts::defaultDetailTemplate();

        return view('livewire.frontend.entry-show', [
            'template' => $template,
            'theme' => $this->collection->settings['theme'] ?? 'greenpeace',
        ]);
    }

    public function title(): string
    {
        return $this->entry->title;
    }
}
