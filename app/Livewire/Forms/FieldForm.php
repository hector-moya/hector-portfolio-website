<?php

namespace App\Livewire\Forms;

use App\Enums\FieldType;
use App\Models\Field;
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

    public string $icon = '';

    public function setField(int $fieldId): void
    {
        $field = \App\Models\Field::query()->findOrFail($fieldId);
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
    }
}
