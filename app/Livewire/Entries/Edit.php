<?php

namespace App\Livewire\Entries;

use App\Livewire\Actions\UpdateEntry;
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
        if (empty($this->form->slug) || $this->form->slug === Str::slug($this->entry->title)) {
            $this->form->slug = Str::slug($this->form->title);
        }
    }

    #[Computed]
    public function blueprint(): ?Blueprint
    {
        if (! $this->form->blueprint_id) {
            return null;
        }

        return Blueprint::with('elements')->find($this->form->blueprint_id);
    }

    public function save(): void
    {
        $this->form->validate();

        (new UpdateEntry)->execute($this->entry, [
            'title' => $this->form->title,
            'slug' => $this->form->slug,
            'status' => $this->form->status,
            'published_at' => $this->form->published_at,
            'fieldValues' => $this->form->fieldValues,
        ]);

        $this->dispatch('notify', message: 'Entry updated successfully.');
        $this->redirect(route('entries'), navigate: true);
    }

    public function render()
    {
        return view('livewire.entries.edit');
    }
}
