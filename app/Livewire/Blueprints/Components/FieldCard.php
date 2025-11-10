<?php

namespace App\Livewire\Blueprints\Components;

use App\Enums\FieldType;
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
        return app(FieldTypeRegistry::class)->all('repeater');
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

    public function removeNestedField(int $fieldId): void
    {
        // Remove the nested field from the form's children collection
        $this->form->destroy($fieldId);
        $this->form->setChildren($this->fieldId);
    }

    public function removeField(): void
    {
        $this->form->destroy($this->fieldId);
        $this->dispatch('field-removed');
    }

    public function addNestedField(string $type = 'text'): void
    {
        $this->field = \App\Models\Field::query()->create([
            'parent_id' => $this->fieldId,
            'blueprint_id' => $this->form->blueprint_id,
            'type' => $type,
            'label' => FieldType::from($type)->defaultLabel(),
            'handle' => $this->generateSlug(FieldType::from($type)->defaultLabel()),
            'instructions' => '',
            'is_required' => false,
            'config' => FieldType::from($type)->defaultConfig(),
            'order' => $this->form->children->count() + 1,
        ]);

        $this->form->children->push($this->field);

        Flux::modal('select-repeater-nested-field-modal')->close();
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
