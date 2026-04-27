@props(['section', 'assets' => collect()])

@php
    $data           = $section['data'];
    $alignmentClass = match ($data['alignment'] ?? 'left') {
        'center' => 'text-center mx-auto',
        'right'  => 'text-right ml-auto',
        default  => 'text-left',
    };
@endphp

<div class="relative overflow-hidden py-16 sm:py-20" style="background: var(--sp-bg);">
    <div class="mx-auto max-w-4xl px-6 lg:px-8">
        <div data-animate class="prose prose-lg max-w-none {{ $alignmentClass }}" style="color: var(--sp-muted); --tw-prose-headings: var(--sp-fg); --tw-prose-links: var(--sp-bio); --tw-prose-bold: var(--sp-fg);">
            {!! nl2br(e($data['content'] ?? '')) !!}
        </div>
    </div>
</div>

<div class="relative z-10" style="height: 1px; background: linear-gradient(90deg, transparent, var(--sp-border), transparent);"></div>
