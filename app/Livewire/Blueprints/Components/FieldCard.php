<?php

namespace App\Livewire\Blueprints\Components;

use App\Livewire\Forms\FieldForm;
use App\Models\Field;
use App\Services\FieldTypeRegistry;
use App\Traits\HasSlug;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Component;

class FieldCard extends Component
{
    use HasSlug;

    public FieldForm $form;

    public Field $field;

    public ?int $fieldId = null;

    public function mount(): void
    {
        $this->form->setField($this->fieldId);
    }

    #[Computed]
    public function fieldTypeOptions(): array
    {
        return app(FieldTypeRegistry::class)->optionsForSelect();
    }

    #[Computed]
    public function fieldTypeMeta(): array
    {
        return app(FieldTypeRegistry::class)->all();
    }

    public function updatedFormLabel(): void
    {
        $this->form->handle = $this->generateSlug($this->form->label);
    }

    public function updateField(int $fieldId): void
    {
        $this->form->update($fieldId);

        $this->dispatch('update-field');

        Flux::modal('field-config-'.$fieldId)->close();
    }

    public function removeField(): void
    {
        $this->form->destroy($this->fieldId);

        $this->dispatch('field-removed');
    }

    public function addNestedField(string $type = 'text'): void
    {
        // Ensure array scaffolding exists
        $this->form->config['blueprint'] ??= [];
        $this->form->config['blueprint'] ??= [];

        // Default config for the chosen type
        $defaults = app(FieldTypeRegistry::class)->defaultConfigFor($type);

        $this->form->config['blueprint'][] = [
            'type' => $type,
            'label' => '',
            'handle' => '',
            'instructions' => '',
            'is_required' => false,
            'config' => $defaults,
        ];
        Flux::modal('select-repeater-nested-field-modal')->close();
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

    public function addOption(): void
    {
        $this->form->config['options'] ??= [];
        $this->form->config['options'][] = ['value' => '', 'label' => ''];
    }

    public function removeOption(int $index): void
    {
        if (! isset($this->form->config['options'][$index])) {
            return;
        }

        unset($this->form->config['options'][$index]);
        $this->form->config['options'] = array_values(
            $this->form->config['options']
        );
    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.blueprints.components.field-card');
    }
}
