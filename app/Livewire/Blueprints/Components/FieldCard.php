<?php

declare(strict_types=1);

namespace App\Livewire\Blueprints\Components;

use App\Enums\SectionType;
use App\Livewire\Forms\FieldForm;
use App\Models\Field;
use App\Services\SectionTypeRegistry;
use App\Traits\HasSlug;
use Flux\Flux;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

final class FieldCard extends Component
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
        return resolve(SectionTypeRegistry::class)->optionsForSelect();
    }

    #[Computed]
    public function fieldTypeMeta(): array
    {
        return resolve(SectionTypeRegistry::class)->all();
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

    public function duplicate(): void
    {
        $original = Field::query()->findOrFail($this->fieldId);

        $copy = $original->replicate(['section_id', 'blueprint_id', 'order']);
        $copy->handle = $original->handle.'_copy';
        $copy->label = $original->label.' (Copy)';
        $copy->order = $original->order + 1;
        $copy->section_id = $original->section_id;
        $copy->blueprint_id = $original->blueprint_id;
        $copy->save();

        $this->dispatch('field-added');
    }

    public function removeField(): void
    {
        $this->form->destroy($this->fieldId);
        $this->dispatch('field-removed');
    }

    public function addNestedField(string $type = 'text'): void
    {
        $sectionType = SectionType::tryFrom($type);

        $this->field = Field::query()->create([
            'parent_id' => $this->fieldId,
            'blueprint_id' => $this->form->blueprint_id,
            'type' => $type,
            'label' => $sectionType?->defaultLabel() ?? 'New Field',
            'handle' => $this->generateSlug($sectionType?->defaultLabel() ?? 'New Field'),
            'instructions' => '',
            'is_required' => false,
            'config' => $sectionType?->defaultConfig() ?? [],
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

    public function render(): View|Factory
    {
        return view('livewire.blueprints.components.field-card');
    }
}
