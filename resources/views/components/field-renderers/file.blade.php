@props(['field', 'value'])
@if($value)
    <flux:link href="{{ $value }}" target="_blank" download>
        {{ basename($value) }}
    </flux:link>
@endif
