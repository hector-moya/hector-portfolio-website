@php
    $onLabel = $field->config['on_label'] ?? 'Yes';
    $offLabel = $field->config['off_label'] ?? 'No';
@endphp

<div class="space-y-2">
    <flux:switch
        label="{{ $field->label }}"
        wire:model="form.fieldValues.{{ $field->handle }}"
    />

    <flux:text size="sm" class="opacity-70">
        {{ $onLabel }} / {{ $offLabel }}
    </flux:text>

    <flux:error name="form.fieldValues.{{ $field->handle }}" />
    @if ($field->instructions)
        <flux:description>{{ $field->instructions }}</flux:description>
    @endif
</div>
