@props(['section', 'assets' => collect()])

@php
    $data       = $section['data'];
    $image      = isset($data['image']) && $data['image'] ? $assets->find($data['image']) : null;
    $imageRight = ($data['image_position'] ?? 'left') === 'right';
    $id         = $section['_id'];
@endphp

<style>
    /* ── Image-text section backgrounds ───────────────────── */
    .sp-imagetext-{{ $id }} {
        /* Light mode: diagonal dawn light */
        background:
            linear-gradient(135deg, oklch(0.92 0.08 82 / 0.22) 0%, transparent 40%),
            linear-gradient(315deg, oklch(0.88 0.07 202 / 0.14) 0%, transparent 40%),
            var(--sp-bg-mid);
    }

    .dark .sp-imagetext-{{ $id }} {
        background: var(--sp-bg-mid);
    }

    /* Image hover treatment (CSS-driven for keyboard/touch parity) */
    .sp-imagetext-{{ $id }} img {
        filter: saturate(0.88) brightness(0.94);
        transition: filter 0.4s;
    }

    .sp-imagetext-{{ $id }}:hover img,
    .sp-imagetext-{{ $id }} a:focus-visible img {
        filter: saturate(1) brightness(1);
    }
</style>

<div class="sp-imagetext-{{ $id }} relative overflow-hidden py-16 sm:py-24">

    {{-- Decorative accent blob --}}
    <div class="pointer-events-none absolute -left-20 -top-20 h-72 w-72 rounded-full opacity-30 blur-3xl dark:opacity-10" style="background: var(--sp-solar);" aria-hidden="true"></div>

    <div class="relative z-10 mx-auto max-w-7xl px-6 lg:px-8">
        <div @class([
            'flex flex-col gap-12 lg:flex-row lg:items-center',
            'lg:flex-row-reverse' => $imageRight,
        ])>

            {{-- Image column --}}
            @if ($image)
                <div data-animate class="lg:w-1/2">
                    <div class="sp-img-border overflow-hidden shadow-2xl">
                        <img
                            src="{{ $image->url }}"
                            alt="{{ $image->alt_text }}"
                            loading="lazy"
                            decoding="async"
                            class="w-full rounded-lg object-cover"
                        />
                    </div>

                    {{-- Dot grid accent --}}
                    <div class="pointer-events-none" style="position: absolute; bottom: -1.5rem; right: -1.5rem; width: 90px; height: 90px; opacity: 0.18;">
                        <svg viewBox="0 0 100 100" width="90" height="90">
                            <defs>
                                <pattern id="dotpat-{{ $id }}" x="0" y="0" width="12" height="12" patternUnits="userSpaceOnUse">
                                    <circle cx="2" cy="2" r="1.2" fill="var(--sp-leaf)"/>
                                </pattern>
                            </defs>
                            <rect width="100" height="100" fill="url(#dotpat-{{ $id }})"/>
                        </svg>
                    </div>
                </div>
            @endif

            {{-- Text column --}}
            <div data-animate data-delay="100" class="{{ $image ? 'lg:w-1/2' : 'w-full' }} space-y-5">

                {{-- Section label --}}
                <div class="text-[0.68rem] font-semibold uppercase tracking-[0.3em]" style="font-family: 'Cinzel', serif; color: var(--sp-leaf); text-shadow: 0 0 12px var(--sp-glow-l);">
                    {{ $data['label'] ?? __('About') }}
                </div>

                @if (!empty($data['title']))
                    <h2 class="text-[clamp(1.8rem,3.5vw,2.8rem)] font-semibold leading-snug" style="font-family: 'Cinzel', serif; color: var(--sp-fg);">
                        {{ $data['title'] }}
                    </h2>
                @endif

                @if (!empty($data['content']))
                    <div class="space-y-3 text-base leading-8" style="color: var(--sp-muted);">
                        {!! nl2br(e($data['content'])) !!}
                    </div>
                @endif

                @if (!empty($data['cta_text']))
                    <div class="pt-2">
                        <a
                            href="{{ $data['cta_url'] ?? '#' }}"
                            class="inline-flex items-center gap-2 rounded border px-6 py-3 text-xs font-semibold uppercase tracking-widest transition-all duration-300 hover:-translate-y-0.5"
                            style="border-color: oklch(from var(--sp-bio) l c h / 0.5); color: var(--sp-bio); box-shadow: 0 0 14px var(--sp-glow-b);"
                        >
                            {{ $data['cta_text'] }}
                            <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                @endif

            </div>
        </div>
    </div>

</div>

<div class="relative z-10" style="height: 1px; background: linear-gradient(90deg, transparent, var(--sp-border), transparent);"></div>
