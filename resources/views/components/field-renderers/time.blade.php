@props(['field', 'value'])
@if($value)
    <div>
        @if(isset($field))
            <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ $field->label ?? '' }}</p>
        @endif
        <p>{{ $value }}</p>
    </div>
@endif
