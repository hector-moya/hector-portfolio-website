@props(['section', 'assets' => collect()])

@php
    $data = $section['data'];
    $id   = $section['_id'];
@endphp

<style>
    /* ── CTA section backgrounds ───────────────────────────── */
    .sp-cta-{{ $id }} {
        /* Light mode: sunbeam conic rays + sky gradient */
        background:
            repeating-conic-gradient(from 270deg at 50% -10%, oklch(0.88 0.10 82 / 0.05) 0deg 2.5deg, transparent 2.5deg 8deg),
            linear-gradient(180deg, oklch(0.90 0.08 202 / 0.22) 0%, oklch(0.95 0.04 88 / 0) 55%),
            var(--sp-bg-mid);
    }

    .dark .sp-cta-{{ $id }} {
        /* Dark mode: radial bioluminescent atmosphere */
        background:
            radial-gradient(ellipse 70% 60% at 50% 50%, oklch(0.20 0.08 155 / 0.8) 0%, transparent 70%),
            var(--sp-bg-mid);
    }

    /* Grid overlay */
    .sp-cta-{{ $id }}::before {
        content: '';
        position: absolute;
        inset: 0;
        background-image:
            linear-gradient(var(--sp-border) 1px, transparent 1px),
            linear-gradient(90deg, var(--sp-border) 1px, transparent 1px);
        background-size: 40px 40px;
        opacity: 0.25;
        pointer-events: none;
    }
</style>

<div class="sp-cta-{{ $id }} relative overflow-hidden">

    {{-- Pulsing orbs — dark mode only --}}
    <div class="pointer-events-none absolute -left-24 -top-24 h-96 w-96 rounded-full hidden dark:block" style="background: oklch(0.78 0.19 82 / 0.11); filter: blur(60px); animation: sp-orb-pulse 6s ease-in-out infinite;" aria-hidden="true"></div>
    <div class="pointer-events-none absolute -bottom-16 -right-16 h-72 w-72 rounded-full hidden dark:block" style="background: oklch(0.72 0.17 178 / 0.11); filter: blur(60px); animation: sp-orb-pulse 6s ease-in-out infinite 3s;" aria-hidden="true"></div>

    {{-- Topographic contour lines — light mode only --}}
    <svg class="absolute inset-0 block dark:hidden" style="width: 100%; height: 100%; pointer-events: none; overflow: hidden;" viewBox="0 0 1440 560" preserveAspectRatio="xMidYMax slice" aria-hidden="true">
        <path d="M-100 460 Q300 400 700 435 Q1100 470 1540 418" fill="none" stroke="oklch(0.55 0.18 82)" stroke-width="1.1" opacity="0.5"/>
        <path d="M-100 495 Q280 435 680 468 Q1080 502 1540 450" fill="none" stroke="oklch(0.55 0.18 82)" stroke-width="0.7" opacity="0.35"/>
        <path d="M-100 425 Q350 358 750 392 Q1150 425 1540 370" fill="none" stroke="oklch(0.45 0.14 202)" stroke-width="0.8" opacity="0.35"/>
    </svg>

    {{-- Corner ornaments --}}
    <svg class="absolute bottom-6 left-6 h-20 w-20 opacity-20" viewBox="0 0 120 120" fill="none" aria-hidden="true" style="transform: scaleY(-1);">
        <path d="M10 110 Q10 10 110 10" stroke="oklch(0.65 0.20 138)" stroke-width="1" fill="none"/>
        <circle cx="10" cy="110" r="4" fill="oklch(0.78 0.19 82)" opacity="0.7"/>
    </svg>
    <svg class="absolute bottom-6 right-6 h-20 w-20 opacity-20" viewBox="0 0 120 120" fill="none" aria-hidden="true" style="transform: scale(-1);">
        <path d="M10 110 Q10 10 110 10" stroke="oklch(0.65 0.20 138)" stroke-width="1" fill="none"/>
        <circle cx="10" cy="110" r="4" fill="oklch(0.72 0.17 178)" opacity="0.7"/>
    </svg>

    {{-- Content --}}
    <div data-animate class="relative z-10 mx-auto max-w-4xl px-6 py-20 text-center sm:py-28 lg:px-8">

        @if (!empty($data['eyebrow']))
            <div class="mb-4 text-[0.68rem] font-semibold uppercase tracking-[0.28em]" style="color: var(--sp-solar); text-shadow: 0 0 14px var(--sp-glow-s);">
                {{ $data['eyebrow'] }}
            </div>
        @endif

        @if (!empty($data['title']))
            <div data-animate data-delay="100">
                <h2 class="text-[clamp(2rem,4vw,3.4rem)] font-bold leading-snug" style="font-family: 'Cinzel', serif; color: var(--sp-fg); text-wrap: balance;">
                    {{ $data['title'] }}
                </h2>
            </div>
        @endif

        @if (!empty($data['content']))
            <div data-animate data-delay="200">
                <p class="mx-auto mt-5 max-w-xl text-base leading-8" style="color: var(--sp-muted);">
                    {!! nl2br(e($data['content'])) !!}
                </p>
            </div>
        @endif

        @if (!empty($data['cta_text']) || !empty($data['secondary_cta_text']))
            <div data-animate data-delay="300" class="mt-10 flex flex-wrap items-center justify-center gap-4">
                @if (!empty($data['cta_text']))
                    <a
                        href="{{ $data['cta_url'] ?? '#' }}"
                        class="inline-flex items-center gap-2 rounded px-8 py-3.5 text-xs font-semibold uppercase tracking-widest transition-all duration-300 hover:-translate-y-0.5"
                        style="background: var(--sp-solar); color: var(--sp-bg); box-shadow: 0 0 28px var(--sp-glow-s);"
                    >
                        {{ $data['cta_text'] }}
                        <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </a>
                @endif
                @if (!empty($data['secondary_cta_text']))
                    <a
                        href="{{ $data['secondary_cta_url'] ?? '#' }}"
                        class="inline-flex items-center gap-2 rounded border px-8 py-3.5 text-xs font-semibold uppercase tracking-widest transition-all duration-300 hover:-translate-y-0.5"
                        style="border-color: oklch(from var(--sp-bio) l c h / 0.5); color: var(--sp-bio); box-shadow: 0 0 14px var(--sp-glow-b);"
                    >
                        {{ $data['secondary_cta_text'] }}
                        <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </a>
                @endif
            </div>
        @endif

    </div>

</div>
