@props([
    'index' => null,
    'parentHandle' => null,
    'field' => null,
])

@php
    $wirePath = $parentHandle !== null
        ? "form.fieldValues.{$parentHandle}.items.{$index}.{$field->handle}"
        : "form.fieldValues.{$field->handle}";
@endphp

<flux:input
    type="number"
    label="{{ $field->label }}"
    min="{{ $field->config['min'] ?? '' }}"
    max="{{ $field->config['max'] ?? '' }}"
    step="{{ $field->config['step'] ?? 1 }}"
    wire:model="{{ $wirePath }}"
/>
<flux:error name="{{ $wirePath }}" />
@if ($field->instructions)
    <flux:description>{{ $field->instructions }}</flux:description>
@endif
