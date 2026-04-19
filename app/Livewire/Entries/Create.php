<?php

declare(strict_types=1);

namespace App\Livewire\Entries;

use App\Enums\SectionType;
use App\Livewire\Forms\EntryForm;
use App\Models\Blueprint;
use App\Models\Collection as ModelsCollection;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

final class Create extends Component
{
    public EntryForm $form;

    public ?int $selectedCollectionId = null;

    /**
     * Section data keyed by field handle.
     *
     * @var array<string, array<string, mixed>>
     */
    public array $sectionValues = [];

    public function mount(): void
    {
        $collections = ModelsCollection::with('blueprint.fields')->get();

        if ($collections->count() === 1) {
            $this->selectedCollectionId = $collections->first()->id;
            $this->form->setCollection($collections->first());
            $this->initializeSectionValues();
        }
    }

    public function updatedSelectedCollectionId(): void
    {
        if ($this->selectedCollectionId !== null && $this->selectedCollectionId !== 0) {
            $collection = ModelsCollection::with('blueprint.fields')->findOrFail($this->selectedCollectionId);
            $this->form->setCollection($collection);
            $this->initializeSectionValues();
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
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function blueprint(): ?Blueprint
    {
        if ($this->form->blueprint_id === null || $this->form->blueprint_id === 0) {
            return null;
        }

        return Blueprint::with(['tabs.sections.fields', 'fields'])->find($this->form->blueprint_id);
    }

    #[On('field-data-updated')]
    public function onFieldDataUpdated(string $handle, array $data): void
    {
        $this->sectionValues[$handle] = $data;
    }

    public function save(): void
    {
        // Sync section values back into the form
        foreach ($this->sectionValues as $handle => $data) {
            $this->form->fieldValues[$handle] = $data;
        }

        $this->form->create();
        $this->dispatch('notify', message: 'Entry created successfully.');
        $this->redirect(route('entries'), navigate: true);
    }

    public function render(): View|Factory
    {
        return view('livewire.entries.create');
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
