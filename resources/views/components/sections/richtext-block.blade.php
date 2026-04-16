@props(['section', 'assets' => collect()])

@php
    $content = $section['data']['content'] ?? '';
@endphp

@if ($content)
    <section class="bg-white py-16 dark:bg-zinc-900">
        <div class="mx-auto max-w-3xl px-6 lg:px-8">
            <div data-animate class="prose prose-lg prose-zinc max-w-none dark:prose-invert">
                {!! $content !!}
            </div>
        </div>
    </section>
@endif
