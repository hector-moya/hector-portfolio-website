@props(['field', 'value'])
@if($value)
    <div class="prose prose-lg dark:prose-invert max-w-none">{!! $value !!}</div>
@endif
