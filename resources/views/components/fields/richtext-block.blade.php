@props(['section', 'assets' => collect()])

@php
    $content = $section['data']['content'] ?? '';
@endphp

@if ($content)
    <section class="relative overflow-hidden py-16" style="background: var(--sp-bg);">
        <div class="mx-auto max-w-3xl px-6 lg:px-8">
            <div data-animate class="prose prose-lg max-w-none" style="color: var(--sp-muted); --tw-prose-headings: var(--sp-fg); --tw-prose-links: var(--sp-bio); --tw-prose-bold: var(--sp-fg); --tw-prose-code: var(--sp-solar);">
                {!! $content !!}
            </div>
        </div>
    </section>

    <div class="relative z-10" style="height: 1px; background: linear-gradient(90deg, transparent, var(--sp-border), transparent);"></div>
@endif
