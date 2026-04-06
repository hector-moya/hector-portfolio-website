@props(['field', 'value'])
@if($value)
    <flux:link href="{{ $value }}" target="_blank" class="break-all">{{ $value }}</flux:link>
@endif
