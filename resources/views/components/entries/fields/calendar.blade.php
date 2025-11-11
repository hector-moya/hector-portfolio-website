@php
    $mode = $field->config['mode'] ?? [];
    $minRange = $field->config['min_range'] ?? null;
    $maxRange = $field->config['max_range'] ?? null;
@endphp

<div class="space-y-2">
    <flux:calendar
        label="{{ $field->label }}"
        wire:model="form.fieldValues.{{ $field->handle }}"
        @if(in_array('multiple', $mode)) multiple @endif
        @if(in_array('range', $mode)) range @endif
        @if($minRange) :min="'{{ $minRange }}'" @endif
        @if($maxRange) :max="'{{ $maxRange }}'" @endif
    />

    <flux:error name="form.fieldValues.{{ $field->handle }}" />
    @if ($field->instructions)
        <flux:description>{{ $field->instructions }}</flux:description>
    @endif
</div>
