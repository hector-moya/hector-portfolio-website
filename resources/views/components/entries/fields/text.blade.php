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
    type="text"
    label="{{ $field->label }}"
    placeholder="{{ $field->config['placeholder'] ?? '' }}"
    maxlength="{{ $field->config['max'] ?? '' }}"
    wire:model="{{ $wirePath }}"
/>
<flux:error name="{{ $wirePath }}" />
@if ($field->instructions)
    <flux:description>{{ $field->instructions }}</flux:description>
@endif
