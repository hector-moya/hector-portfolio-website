@props([
    'index' => null,
    'parentHandle' => null,
    'field' => null,
])

@php
    $wirePath = $parentHandle !== null
        ? "form.fieldValues.{$parentHandle}.items.{$index}.{$field->handle}"
        : "form.fieldValues.{$field->handle}";
    $onLabel = $field->config['on_label'] ?? 'Yes';
    $offLabel = $field->config['off_label'] ?? 'No';
@endphp

<div class="space-y-2">
    <flux:switch
        label="{{ $field->label }}"
        wire:model="{{ $wirePath }}"
    />

    <flux:text size="sm" class="opacity-70">
        {{ $onLabel }} / {{ $offLabel }}
    </flux:text>

    <flux:error name="{{ $wirePath }}" />
    @if ($field->instructions)
        <flux:description>{{ $field->instructions }}</flux:description>
    @endif
</div>
