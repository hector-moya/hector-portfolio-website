@props(['section', 'assets' => collect()])

@php
    $data = $section['data'];
    $alignmentClass = match ($data['alignment'] ?? 'left') {
        'center' => 'text-center',
        'right' => 'text-right',
        default => 'text-left',
    };
@endphp

<div class="bg-white py-16 dark:bg-zinc-900 sm:py-20">
    <div class="mx-auto max-w-4xl px-6 lg:px-8">
        <div class="prose prose-lg dark:prose-invert max-w-none {{ $alignmentClass }}">
            {!! nl2br(e($data['content'] ?? '')) !!}
        </div>
    </div>
</div>
