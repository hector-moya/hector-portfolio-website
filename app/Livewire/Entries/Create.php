<?php

namespace App\Livewire\Entries;

use App\Livewire\Forms\EntryForm;
use App\Models\Blueprint;
use App\Models\Collection as ModelsCollection;
use Flux\Flux;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class Create extends Component
{
    public EntryForm $form;

    public ?int $selectedCollectionId = null;

    public function mount(): void
    {
        // Auto-select collection if only one exists
        $collections = ModelsCollection::with('blueprint.fields')->get();

        if ($collections->count() === 1) {
            $this->selectedCollectionId = $collections->first()->id;
            $this->form->setCollection($collections->first());
        }
    }

    public function updatedSelectedCollectionId(): void
    {
        if ($this->selectedCollectionId !== null && $this->selectedCollectionId !== 0) {
            $collection = ModelsCollection::with('blueprint.fields')->findOrFail($this->selectedCollectionId);
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

        return Blueprint::with(['tabs.sections.fields.children', 'fields.children'])->find($this->form->blueprint_id);
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

    public function openAssetBrowser(string $handle): void
    {
        // Open the modal for the specific field
        Flux::modal('asset-browser-'.$handle)->show();
    }

    #[On('asset-selected')]
    public function onAssetSelected(array $data): void
    {
        // Update the form field value
        $this->form->fieldValues[$data['handle']] = $data['value'];
    }

    public function render(): View|Factory
    {
        return view('livewire.entries.create');
    }
}
