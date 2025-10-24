<?php

namespace App\Livewire\Entries;

use App\Livewire\Forms\EntryForm;
use App\Models\Blueprint;
use App\Models\Entry;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Edit extends Component
{
    public EntryForm $form;

    public Entry $entry;

    public function mount(Entry $entry): void
    {
        $this->entry = $entry->load('collection.blueprint.elements', 'elements');
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

        return Blueprint::with('elements')->find($this->form->blueprint_id);
    }

    public function save(): void
    {
        $this->form->update($this->form->entry->id);

        $this->dispatch('notify', message: 'Entry updated successfully.');
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
        return view('livewire.entries.edit');
    }
}
