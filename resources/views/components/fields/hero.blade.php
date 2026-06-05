@props(['section', 'assets' => collect()])

@php
    $data    = $section['data'];
    $bgImage = isset($data['bg_image']) && $data['bg_image'] ? $assets->find($data['bg_image']) : null;
    $id      = $section['_id'];
@endphp

<style>
    /* ── Hero section backgrounds ─────────────────────────── */
    .sp-hero-{{ $id }} {
        /* Light mode: sunbeams + warm sky fade */
        background:
            repeating-conic-gradient(from 270deg at 50% -12%, oklch(0.88 0.12 82 / 0.055) 0deg 2.5deg, transparent 2.5deg 8deg),
            linear-gradient(180deg, oklch(0.90 0.10 82 / 0.45) 0%, oklch(0.96 0.04 88 / 0.05) 45%, transparent 68%),
            var(--sp-bg);
    }

    .dark .sp-hero-{{ $id }} {
        /* Dark mode: bioluminescent radial gradients */
        background:
            radial-gradient(ellipse 80% 60% at 50% 0%,  oklch(0.22 0.09 142 / 0.45) 0%, transparent 65%),
            radial-gradient(ellipse 50% 40% at 80% 80%, oklch(0.20 0.08 178 / 0.28) 0%, transparent 60%),
            var(--sp-bg);
    }

    /* Grid overlay with radial fade mask */
    .sp-hero-{{ $id }}::before {
        content: '';
        position: absolute;
        inset: 0;
        background-image:
            linear-gradient(var(--sp-border) 1px, transparent 1px),
            linear-gradient(90deg, var(--sp-border) 1px, transparent 1px);
        background-size: 56px 56px;
        opacity: 0.4;
        mask-image: radial-gradient(ellipse 90% 70% at 50% 0%, black 0%, transparent 70%);
        pointer-events: none;
    }

    /* Sunbeam breathing glow — light mode only */
    html:not(.dark) .sp-hero-{{ $id }}::after {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(ellipse 60% 40% at 50% -10%, oklch(0.92 0.12 82 / 0.18) 0%, transparent 60%);
        animation: sp-sunbreath 5s ease-in-out infinite;
        pointer-events: none;
    }
</style>

<div class="sp-hero-{{ $id }} relative min-h-screen overflow-hidden" style="display: flex; align-items: center; justify-content: center;">

    {{-- BG image override (when set in CMS) --}}
    @if ($bgImage)
        <div class="absolute inset-0">
            <img src="{{ $bgImage->url }}" alt="{{ $bgImage->alt_text }}" class="h-full w-full object-cover" />
            <div class="absolute inset-0 bg-black/55"></div>
            <div class="absolute inset-0" style="background: linear-gradient(135deg, oklch(0.10 0.04 145 / 0.70), oklch(0.14 0.055 148 / 0.40), oklch(0.10 0.04 145 / 0.70));"></div>
        </div>
    @endif

    {{-- Corner ornaments --}}
    <svg class="absolute left-6 top-6 h-24 w-24 opacity-20" viewBox="0 0 120 120" fill="none" aria-hidden="true">
        <path d="M10 110 Q10 10 110 10" stroke="oklch(0.65 0.20 138)" stroke-width="1" fill="none"/>
        <path d="M10 90 Q30 10 110 10"  stroke="oklch(0.65 0.20 138)" stroke-width="0.5" fill="none" opacity="0.5"/>
        <circle cx="10" cy="10" r="4" fill="oklch(0.78 0.19 82)" opacity="0.7"/>
        <circle cx="110" cy="10" r="2" fill="oklch(0.72 0.17 178)" opacity="0.5"/>
    </svg>
    <svg class="absolute right-6 top-6 h-24 w-24 -scale-x-100 opacity-20" viewBox="0 0 120 120" fill="none" aria-hidden="true">
        <path d="M10 110 Q10 10 110 10" stroke="oklch(0.65 0.20 138)" stroke-width="1" fill="none"/>
        <circle cx="10" cy="10" r="4" fill="oklch(0.78 0.19 82)" opacity="0.7"/>
    </svg>

    {{-- Content --}}
    <div class="relative z-10 mx-auto max-w-4xl px-6 pb-24 pt-32 text-center sm:pb-32 sm:pt-40 lg:px-8">

        {{-- Eyebrow / section badge --}}
        <div class="sp-entrance-1 mb-6 text-xs font-semibold uppercase tracking-[0.28em]" style="color: var(--sp-solar); text-shadow: 0 0 16px var(--sp-glow-s);">
            {{ \App\Support\Seo::siteName() }}
        </div>

        {{-- Main title --}}
        @if (!empty($data['title']))
            <div class="sp-entrance-2">
                <h1 class="text-[clamp(2.4rem,6vw,5.5rem)] font-black leading-[1.08] tracking-tight" style="font-family: 'Cinzel', serif;">
                    <span style="color: var(--sp-fg);">{{ $data['title'] }}</span>
                </h1>
            </div>
        @endif

        {{-- Subtitle --}}
        @if (!empty($data['subtitle']))
            <div class="sp-entrance-3 mt-3">
                <p class="text-xl font-semibold" style="font-family: 'Cinzel', serif; color: var(--sp-bio); text-shadow: 0 0 40px var(--sp-glow-b); animation: sp-glow-bio 4s ease-in-out infinite 2s;">
                    {{ $data['subtitle'] }}
                </p>
            </div>
        @endif

        {{-- Body text --}}
        @if (!empty($data['content']))
            <div class="sp-entrance-4 mx-auto mt-6 max-w-xl">
                <p class="text-lg leading-8" style="color: var(--sp-muted);">
                    {!! nl2br(e($data['content'])) !!}
                </p>
            </div>
        @endif

        {{-- CTA buttons --}}
        @php
            $hasPrimaryCta = ! empty($data['cta_text']) && ! empty($data['cta_url']);
            $hasSecondaryCta = ! empty($data['secondary_cta_text']) && ! empty($data['secondary_cta_url']);
        @endphp
        @if ($hasPrimaryCta || $hasSecondaryCta)
            <div class="sp-entrance-5 mt-10 flex flex-wrap items-center justify-center gap-4">
                @if ($hasPrimaryCta)
                    <a
                        href="{{ $data['cta_url'] }}"
                        class="sp-btn-solar inline-flex items-center gap-2 rounded px-8 py-3.5 text-xs font-semibold uppercase tracking-widest transition-all duration-300 hover:-translate-y-0.5"
                    >
                        {{ $data['cta_text'] }}
                    </a>
                @endif
                @if ($hasSecondaryCta)
                    <a
                        href="{{ $data['secondary_cta_url'] }}"
                        class="sp-btn-bio inline-flex items-center gap-2 rounded border px-8 py-3.5 text-xs font-semibold uppercase tracking-widest transition-all duration-300 hover:-translate-y-0.5"
                    >
                        {{ $data['secondary_cta_text'] }}
                        <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </a>
                @endif
            </div>
        @endif

    </div>

    {{-- Scroll hint --}}
    <div class="sp-entrance-hint absolute bottom-10 left-1/2 flex flex-col items-center gap-1.5" style="transform: translateX(-50%); animation: sp-float-hint 3s ease-in-out infinite, sp-fade-up 0.5s 1.65s ease both; color: var(--sp-muted);">
        <span class="text-[0.65rem] uppercase tracking-[0.15em]">{{ __('Scroll') }}</span>
        <div class="w-px" style="height: 44px; background: linear-gradient(to bottom, var(--sp-leaf), transparent); animation: sp-scroll-line 2.5s ease-in-out infinite;"></div>
    </div>

    {{-- Topographic contour lines — light mode only --}}
    <svg class="absolute bottom-0 left-0 right-0 block dark:hidden" style="height: 200px; pointer-events: none;" viewBox="0 0 1440 200" preserveAspectRatio="xMidYMax slice" aria-hidden="true">
        <path d="M-100 160 Q300 100 700 135 Q1100 170 1540 120" fill="none" stroke="oklch(0.55 0.18 82)" stroke-width="1.2" opacity="0.55"/>
        <path d="M-100 185 Q280 125 680 160 Q1080 195 1540 145" fill="none" stroke="oklch(0.55 0.18 82)" stroke-width="0.8" opacity="0.38"/>
        <path d="M-100 140 Q350 75  750 110 Q1150 145 1540 92"  fill="none" stroke="oklch(0.45 0.14 202)" stroke-width="0.7" opacity="0.32"/>
    </svg>

</div>

<div class="relative z-10" style="height: 1px; background: linear-gradient(90deg, transparent, var(--sp-border), transparent);"></div>
