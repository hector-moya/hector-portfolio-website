@props(['field', 'value'])
@if($value)
    <img src="{{ $value }}" alt="{{ $field->label }}" class="w-full rounded-lg object-cover" />
@endif
