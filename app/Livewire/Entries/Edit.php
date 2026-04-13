<?php

declare(strict_types=1);

namespace App\Livewire\Entries;

use App\Enums\SectionType;
use App\Livewire\Forms\EntryForm;
use App\Models\Blueprint;
use App\Models\Entry;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Component;

final class Edit extends Component
{
    public EntryForm $form;

    public Entry $entry;

    /**
     * Section data keyed by field handle.
     * Each value is a section data array (e.g., ['title' => '', 'content' => '', ...]).
     *
     * @var array<string, array<string, mixed>>
     */
    public array $sectionValues = [];

    public function mount(Entry $entry): void
    {
        $this->entry = $entry->load('collection.blueprint.tabs.sections.fields', 'collection.blueprint.fields', 'elements.field');
        $this->form->setEntry($this->entry);
        $this->initializeSectionValues();
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

    public function save(): void
    {
        // Sync section values back into the form before validation and persistence
        foreach ($this->sectionValues as $handle => $data) {
            $this->form->fieldValues[$handle] = $data;
        }

        // $this->form->validate();
        $this->form->update($this->form->entry->id);
    }

    public function render(): View|Factory
    {
        return view('livewire.entries.edit');
    }

    private function initializeSectionValues(): void
    {
        $blueprint = $this->blueprint;

        if (! $blueprint instanceof Blueprint) {
            return;
        }

        foreach ($blueprint->fields as $field) {
            $sectionType = SectionType::tryFrom($field->type);
            if ($sectionType) {
                $this->sectionValues[$field->handle] = $this->form->fieldValues[$field->handle]
                    ?? $sectionType->defaultData();
            }
        }
    }
}
