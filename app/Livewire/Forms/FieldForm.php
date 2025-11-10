<?php

namespace App\Livewire\Forms;

use App\Enums\FieldType;
use App\Models\Field;
use Illuminate\Support\Collection;
use Livewire\Form;

class FieldForm extends Form
{
    public ?int $blueprint_id = null;

    public ?int $section_id = null;

    public string $type = '';

    public string $label = '';

    public string $handle = '';

    public string $instructions = '';

    public array $config = [];

    public bool $is_required = false;

    public ?int $order = null;
    public ?Collection $children = null;
    public ?int $parent_id = null;

    public string $icon = '';

    public function setField(int $fieldId): void
    {
        $field = Field::query()->findOrFail($fieldId);
        $this->blueprint_id = $field->blueprint_id;
        $this->section_id = $field->section_id;
        $this->type = $field->type;
        $this->label = $field->label;
        $this->handle = $field->handle;
        $this->icon = FieldType::from($field->type)->icon();
        $this->instructions = $field->instructions ?? '';
        $this->config = $field->config ?? [];
        $this->is_required = $field->is_required;
        $this->order = $field->order;
        $this->children = $field->children;
        $this->parent_id = $field->parent_id;
    }

    public function setChildren(?int $parentId): void
    {
        $this->children = Field::query()
            ->where('parent_id', $parentId)
            ->orderBy('order')
            ->get();
    }

    public function update(int $fieldId): void
    {
        $field = Field::query()->findOrFail($fieldId);

        $field->update([
            'blueprint_id' => $this->blueprint_id,
            'section_id' => $this->section_id,
            'type' => $this->type,
            'label' => $this->label,
            'handle' => $this->handle,
            'instructions' => $this->instructions,
            'config' => $this->config,
            'is_required' => $this->is_required,
            'order' => $this->order,
        ]);
    }

    public function destroy(int $fieldId): void
    {
        Field::query()->findOrFail($fieldId)->delete();
    }
}
