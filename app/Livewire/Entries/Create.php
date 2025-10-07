<?php

namespace App\Livewire\Entries;

use App\Livewire\Actions\CreateEntry;
use App\Livewire\Forms\EntryForm;
use App\Models\Blueprint;
use App\Models\Collection;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Create extends Component
{
    public EntryForm $form;

    public ?int $selectedCollectionId = null;

    public function mount(): void
    {
        // Auto-select collection if only one exists
        $collections = Collection::with('blueprint.elements')->get();

        if ($collections->count() === 1) {
            $this->selectedCollectionId = $collections->first()->id;
            $this->form->setCollection($collections->first());
        }
    }

    public function updatedSelectedCollectionId(): void
    {
        if ($this->selectedCollectionId !== null && $this->selectedCollectionId !== 0) {
            $collection = Collection::with('blueprint.elements')->findOrFail($this->selectedCollectionId);
            $this->form->setCollection($collection);
        }
    }

    public function updatedFormTitle(): void
    {
        if ($this->form->slug === '' || $this->form->slug === '0') {
            $this->form->slug = Str::slug($this->form->title);
        }
    }

    #[Computed]
    public function collections()
    {
        return Collection::query()
            ->with('blueprint')
            ->where('is_active', true)
            ->orderBy('title')
            ->get();
    }

    #[Computed]
    public function blueprint(): ?Blueprint
    {
        if ($this->form->blueprint_id === null || $this->form->blueprint_id === 0) {
            return null;
        }

        return Blueprint::with('elements')->find($this->form->blueprint_id);
    }

    public function save(): void
    {
        $this->form->validate();

        (new CreateEntry)->execute([
            'collection_id' => $this->form->collection_id,
            'blueprint_id' => $this->form->blueprint_id,
            'title' => $this->form->title,
            'slug' => $this->form->slug,
            'status' => $this->form->status,
            'published_at' => $this->form->published_at,
            'fieldValues' => $this->form->fieldValues,
        ]);

        $this->dispatch('notify', message: 'Entry created successfully.');
        $this->redirect(route('entries'), navigate: true);
    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.entries.create');
    }
}
