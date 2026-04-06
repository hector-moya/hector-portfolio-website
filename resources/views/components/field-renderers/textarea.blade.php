@props(['field', 'value'])
@if($value)
    <p class="text-zinc-700 dark:text-zinc-300">{{ $value }}</p>
@endif
