@props([
    'index' => null,
    'parentHandle' => null,
    'field' => null,
])

@php
    $wirePath = $parentHandle !== null
        ? "form.fieldValues.{$parentHandle}.items.{$index}.{$field->handle}"
        : "form.fieldValues.{$field->handle}";
    $options = $field->config['options'] ?? [];
@endphp

<div class="space-y-2">
    <flux:heading size="sm">{{ $field->label }}</flux:heading>

    @if ($field->instructions)
        <flux:description>{{ $field->instructions }}</flux:description>
    @endif

    <div class="space-y-2">
        @foreach($options as $option)
            @php
                $value = is_array($option) ? ($option['value'] ?? $option['label']) : $option;
                $label = is_array($option) ? ($option['label'] ?? $option['value']) : $option;
            @endphp
            <flux:radio
                label="{{ $label }}"
                name="{{ $wirePath }}"
                value="{{ $value }}"
                wire:model="{{ $wirePath }}"
            />
        @endforeach
    </div>

    <flux:error name="{{ $wirePath }}" />
</div>
