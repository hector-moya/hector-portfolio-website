@props(['field', 'value'])
@if($value)
    @php
        $label = collect($field->config['options'] ?? [])->firstWhere('value', $value)['label'] ?? $value;
    @endphp
    <flux:badge>{{ $label }}</flux:badge>
@endif
