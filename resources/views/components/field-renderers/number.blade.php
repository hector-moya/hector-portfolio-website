@props(['field', 'value'])
@if($value !== null && $value !== '')
    <flux:text>{{ $value }}</flux:text>
@endif
