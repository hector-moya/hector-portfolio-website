<?php

namespace App\Livewire\Blueprints\Components;

use App\Livewire\Forms\FieldForm;
use App\Models\Field;
use App\Services\FieldTypeRegistry;
use Livewire\Attributes\Computed;
use Livewire\Component;

class FieldCard extends Component
{
    public FieldForm $form;

    public Field $field;

    public ?int $fieldId = null;

    public function mount(): void
    {
        $this->form->setField($this->fieldId);
    }

    /**
     * Select options [value => label] built from registry.
     */
    #[Computed]
    public function fieldTypeOptions(): array
    {
        return app(FieldTypeRegistry::class)->optionsForSelect();
    }

    public function removeField(int $index): void
    {
        unset($this->fields[$index]);
        $this->fields = array_values($this->fields);
    }

    public function updateHandleFromLabel(int $index): void
    {
        $this->fields[$index]['handle'] = $this->generateSlug($this->fields[$index]['label']);
    }

    public function addNestedField(int $parentIndex, string $type = 'text'): void
    {
        // Ensure array scaffolding exists
        $this->fields[$parentIndex]['config'] ??= [];
        $this->fields[$parentIndex]['config']['blueprint'] ??= [];

        // Default config for the chosen type
        $defaults = app(FieldTypeRegistry::class)->defaultConfigFor($type);

        $this->fields[$parentIndex]['config']['blueprint'][] = [
            'type' => $type,
            'label' => '',
            'handle' => '',
            'instructions' => '',
            'is_required' => false,
            'config' => $defaults,
        ];
    }

    public function removeNestedField(int $parentIndex, int $childIndex): void
    {
        if (! isset($this->fields[$parentIndex]['config']['blueprint'][$childIndex])) {
            return;
        }

        unset($this->fields[$parentIndex]['config']['blueprint'][$childIndex]);
        $this->fields[$parentIndex]['config']['blueprint'] = array_values(
            $this->fields[$parentIndex]['config']['blueprint']
        );
    }

    /**
     * Generic helpers for option-based types (select/radio) while editing blueprint.
     */
    public function addOption(int $index): void
    {
        $this->fields[$index]['config'] ??= [];
        $this->fields[$index]['config']['options'] ??= [];
        $this->fields[$index]['config']['options'][] = ['value' => '', 'label' => ''];
    }

    public function removeOption(int $index, int $optIndex): void
    {
        if (! isset($this->fields[$index]['config']['options'][$optIndex])) {
            return;
        }

        unset($this->fields[$index]['config']['options'][$optIndex]);
        $this->fields[$index]['config']['options'] = array_values(
            $this->fields[$index]['config']['options']
        );
    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.blueprints.components.field-card');
    }
}
