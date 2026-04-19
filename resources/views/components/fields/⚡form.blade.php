<?php

use App\Models\Field;
use Livewire\Attributes\Modelable;
use Livewire\Component;

new class extends Component {
    public Field $field;

    #[Modelable]
    public array $data = [];

    public function mount(Field $field): void
    {
        $this->field = $field;
    }

    public function updatedData(): void
    {
        $this->dispatch('field-data-updated', handle: $this->field->handle, data: $this->data);
    }

    public function addFormField(): void
    {
        $this->data['fields'][] = ['type' => 'text', 'label' => '', 'handle' => '', 'required' => false];
        $this->dispatch('field-data-updated', handle: $this->field->handle, data: $this->data);
    }

    public function removeFormField(int $index): void
    {
        array_splice($this->data['fields'], $index, 1);
        $this->data['fields'] = array_values($this->data['fields']);
        $this->dispatch('field-data-updated', handle: $this->field->handle, data: $this->data);
    }
};
?>

@php
$availableFieldTypes = [
    'text' => 'Text',
    'textarea' => 'Textarea',
    'email' => 'Email',
    'number' => 'Number',
    'date' => 'Date',
    'time' => 'Time',
    'toggle' => 'Toggle',
    'select' => 'Select',
    'radio' => 'Radio',
];
@endphp

<div class="space-y-4">
    <flux:input label="{{ __('Section Title') }}" wire:model.blur="data.title" />

    <div class="space-y-3">
        <flux:label>{{ __('Form Fields') }}</flux:label>

        @foreach ($data['fields'] ?? [] as $fieldIdx => $formField)
            <div wire:key="form-field-{{ $field->handle }}-{{ $fieldIdx }}" class="space-y-3 rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
                <div class="flex items-center justify-between">
                    <flux:text size="sm" class="font-medium">{{ __('Field :number', ['number' => $fieldIdx + 1]) }}</flux:text>
                    <flux:button type="button" variant="ghost" size="sm" icon="trash" wire:click="removeFormField({{ $fieldIdx }})" class="text-red-500" />
                </div>
                <flux:select label="{{ __('Field Type') }}" wire:model.live="data.fields.{{ $fieldIdx }}.type">
                    @foreach ($availableFieldTypes as $value => $label)
                        <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:input label="{{ __('Label') }}" wire:model.blur="data.fields.{{ $fieldIdx }}.label" />
                <flux:input label="{{ __('Handle') }}" wire:model.blur="data.fields.{{ $fieldIdx }}.handle" description="{{ __('Unique identifier for this field') }}" />
                <flux:checkbox label="{{ __('Required') }}" wire:model.live="data.fields.{{ $fieldIdx }}.required" />
            </div>
        @endforeach

        <flux:button type="button" variant="ghost" size="sm" icon="plus" wire:click="addFormField">
            {{ __('Add Field') }}
        </flux:button>
    </div>
</div>
