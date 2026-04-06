@props(['field', 'value'])
@if($value)
    <flux:link href="mailto:{{ $value }}">{{ $value }}</flux:link>
@endif
