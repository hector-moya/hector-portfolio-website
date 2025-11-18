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
    type="email"
    label="{{ $field->label }}"
    placeholder="{{ $field->config['placeholder'] ?? '' }}"
    wire:model="{{ $wirePath }}"
/>
<flux:error name="{{ $wirePath }}" />
@if ($field->instructions)
    <flux:description>{{ $field->instructions }}</flux:description>
@endif
