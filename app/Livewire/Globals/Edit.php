<?php

declare(strict_types=1);

namespace App\Livewire\Globals;

use App\Enums\SectionType;
use App\Livewire\Forms\GlobalForm;
use App\Models\Blueprint;
use App\Models\GlobalSet;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

final class Edit extends Component
{
    public GlobalForm $form;

    public GlobalSet $globalSet;

    /**
     * Section data keyed by field handle.
     *
     * @var array<string, array<string, mixed>>
     */
    public array $sectionValues = [];

    public function mount(GlobalSet $globalSet): void
    {
        $this->globalSet = $globalSet->load('blueprint.tabs.sections.fields', 'blueprint.fields', 'variables');
        $this->form->setGlobalSet($this->globalSet);
        $this->initializeSectionValues();
    }

    public function updatedFormBlueprintId(): void
    {
        $this->sectionValues = [];
        $this->initializeSectionValues();
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
        foreach ($this->sectionValues as $handle => $data) {
            $this->form->variables[$handle] = $data;
        }

        $this->form->update($this->globalSet->id);
    }

    public function render(): View
    {
        return view('livewire.globals.edit', [
            'blueprints' => Blueprint::all(),
        ]);
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
                $this->sectionValues[$field->handle] = $this->form->variables[$field->handle]
                    ?? $sectionType->defaultData();
            }
        }
    }
}
