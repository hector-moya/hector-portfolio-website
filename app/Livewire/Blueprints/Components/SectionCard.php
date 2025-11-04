<?php

namespace App\Livewire\Blueprints\Components;

use App\Livewire\Forms\BlueprintForm;
use App\Services\FieldTypeRegistry;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Component;

class SectionCard extends Component
{
    public array $section;
    public BlueprintForm $form;
    public int $tabIndex;
    public int $sectionIndex;

    public function mount(array $section, BlueprintForm $form, int $tabIndex, int $sectionIndex): void
    {
        $this->section = $section;
        $this->form = $form;
        $this->tabIndex = $tabIndex;
        $this->sectionIndex = $sectionIndex;
    }

    #[Computed]
    public function fieldTypeMeta(): array
    {
        return app(FieldTypeRegistry::class)->all();
    }

    public function addField(string $type): void
    {
        $defaultConfig = app(FieldTypeRegistry::class)->defaultConfigFor($type);

        $this->form->tabs[$this->tabIndex]['sections'][$this->sectionIndex]['fields'] ??= [];
        $this->form->tabs[$this->tabIndex]['sections'][$this->sectionIndex]['fields'][] = [
            'name' => __('New Field'),
            'handle' => '',
            'type' => $type,
            'sort_order' => count($this->form->tabs[$this->tabIndex]['sections'][$this->sectionIndex]['fields']),
            'instructions' => '',
            'required' => false,
            'config' => $defaultConfig,
            'validation' => [],
        ];

        Flux::modal('select-field-modal')->close();
    }

    public function removeField(int $fieldIndex): void
    {
        unset($this->form->tabs[$this->tabIndex]['sections'][$this->sectionIndex]['fields'][$fieldIndex]);
        $this->form->tabs[$this->tabIndex]['sections'][$this->sectionIndex]['fields'] = array_values(
            $this->form->tabs[$this->tabIndex]['sections'][$this->sectionIndex]['fields']
        );
    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.blueprints.components.section-card');
    }
}
