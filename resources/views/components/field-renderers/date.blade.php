@props(['field', 'value'])
@if($value)
    <flux:text>{{ \Carbon\Carbon::parse($value)->format('F j, Y') }}</flux:text>
@endif
