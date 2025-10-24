<?php

namespace App\Livewire\Entries;

use App\Livewire\Forms\EntryForm;
use App\Models\Blueprint;
use App\Models\Collection as ModelsCollection;
use Illuminate\Support\Collection;
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
        $collections = ModelsCollection::with('blueprint.elements')->get();

        if ($collections->count() === 1) {
            $this->selectedCollectionId = $collections->first()->id;
            $this->form->setCollection($collections->first());
        }
    }

    public function updatedSelectedCollectionId(): void
    {
        if ($this->selectedCollectionId !== null && $this->selectedCollectionId !== 0) {
            $collection = ModelsCollection::with('blueprint.elements')->findOrFail($this->selectedCollectionId);
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
    public function collections(): Collection
    {
        return ModelsCollection::query()
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
        $this->form->create();
        $this->dispatch('notify', message: 'Entry created successfully.');
        $this->redirect(route('entries'), navigate: true);
    }

    public function addRepeaterItem(string $handle): void
    {
        $this->form->addRepeaterItem($handle);
    }

    public function removeRepeaterItem(string $handle, int $index): void
    {
        $this->form->removeRepeaterItem($handle, $index);
    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.entries.create');
    }
}
