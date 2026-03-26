<?php

namespace App\Livewire\Entries;

use App\Livewire\Forms\EntryForm;
use App\Models\Blueprint;
use App\Models\Entry;
use Flux\Flux;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class Edit extends Component
{
    use WithFileUploads;

    public EntryForm $form;

    public array $uploads = [];

    public Entry $entry;

    public function mount(Entry $entry): void
    {
        $this->entry = $entry->load('collection.blueprint.tabs.sections.fields', 'collection.blueprint.fields', 'elements');
        $this->form->setEntry($this->entry);
    }

    public function updatedFormTitle(): void
    {
        if (in_array($this->form->slug, ['', '0', Str::slug($this->entry->title)], true)) {
            $this->form->slug = Str::slug($this->form->title);
        }
    }

    #[Computed]
    public function blueprint(): ?Blueprint
    {
        if ($this->form->blueprint_id === null || $this->form->blueprint_id === 0) {
            return null;
        }

        return Blueprint::with(['tabs.sections.fields', 'fields'])->find($this->form->blueprint_id);
    }

    #[On('asset-uploaded')]
    public function save(): void
    {
        $this->form->update($this->form->entry->id);

        $this->dispatch('notify', message: 'Entry updated successfully.');
        // $this->redirect(route('entries'), navigate: true);
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
        // Page builder asset handles are handled by the PageBuilder component
        if (str_starts_with($data['handle'], 'section_')) {
            return;
        }

        // Update the form field value
        $this->form->fieldValues[$data['handle']] = $data['value'];
    }

    public function render(): View|Factory
    {
        return view('livewire.entries.edit');
    }
}
