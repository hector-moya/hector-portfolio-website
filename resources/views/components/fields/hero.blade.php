@props(['section', 'assets' => collect()])

@php
    $data = $section['data'];
    $bgImage = isset($data['bg_image']) && $data['bg_image'] ? $assets->find($data['bg_image']) : null;
@endphp

{{--
    No background set on this element intentionally — the greenpeace theme wrapper
    provides the gradient. For bg_image entries the image + gradient overlay are
    absolutely positioned children so they sit in the normal stacking order.
--}}
<div class="relative overflow-hidden text-white">
    {{-- Background image + gradient overlay (rendered when a bg image is set) --}}
    @if ($bgImage)
        <div class="absolute inset-0">
            <img
                src="{{ $bgImage->url }}"
                alt="{{ $bgImage->alt_text }}"
                class="h-full w-full object-cover"
            />
            {{-- Flat dark base so text is always readable --}}
            <div class="absolute inset-0 bg-black/55"></div>
            {{-- Tinted gradient on top for the brand feel --}}
            <div class="absolute inset-0 bg-linear-to-br from-teal-950/70 via-zinc-900/40 to-green-950/70"></div>
        </div>
    @endif

    {{-- Decorative grid overlay --}}
    <div class="absolute inset-0 overflow-hidden" aria-hidden="true">
        <svg class="absolute inset-0 h-full w-full stroke-white/5" viewBox="0 0 800 600" preserveAspectRatio="none">
            <defs>
                <pattern id="hero-grid-{{ $section['_id'] }}" width="80" height="80" patternUnits="userSpaceOnUse">
                    <path d="M 80 0 L 0 0 0 80" fill="none" stroke-width="0.5" />
                </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#hero-grid-{{ $section['_id'] }})" />
        </svg>
        <div class="absolute right-0 top-0 h-96 w-96 -translate-y-1/2 translate-x-1/3 rounded-full bg-teal-100/20 dark:bg-teal-500/20 blur-3xl"></div>
        <div class="absolute bottom-0 left-0 h-80 w-80 translate-y-1/2 -translate-x-1/3 rounded-full bg-green-100/15 dark:bg-green-500/15 blur-3xl"></div>
    </div>

    <div class="relative mx-auto max-w-7xl px-6 pb-24 pt-32 sm:pb-32 sm:pt-40 lg:px-8">
        <div class="mx-auto max-w-3xl">
            @if (!empty($data['title']))
                <div data-animate>
                    <flux:heading class="text-5xl! font-bold leading-tight tracking-tight sm:text-6xl! lg:text-7xl!" level="1">
                        {{ $data['title'] }}
                    </flux:heading>
                </div>
            @endif

            @if (!empty($data['subtitle']))
                <div data-animate data-delay="100">
                    <flux:heading size="xl" class="mt-4 font-normal! text-zinc-400 dark:text-teal-300">
                        {{ $data['subtitle'] }}
                    </flux:heading>
                </div>
            @endif

            @if (!empty($data['content']))
                <div data-animate data-delay="200">
                    <flux:text size="xl" class="mt-6 text-lg leading-8 text-zinc-400 dark:text-zinc-400">
                        {!! nl2br(e($data['content'])) !!}
                    </flux:text>
                </div>
            @endif

            @if (!empty($data['cta_text']) || !empty($data['secondary_cta_text']))
                <div data-animate data-delay="300" class="mt-10 flex flex-wrap items-center gap-4">
                    @if (!empty($data['cta_text']))
                        <flux:button variant="primary" href="{{ $data['cta_url'] ?? '#' }}" class="shadow-xl shadow-teal-900/40">
                            {{ $data['cta_text'] }}
                        </flux:button>
                    @endif
                    @if (!empty($data['secondary_cta_text']))
                        <flux:button href="{{ $data['secondary_cta_url'] ?? '#' }}" class="shadow-xl! shadow-zinc-400/40">
                            {{ $data['secondary_cta_text'] }}
                            <flux:icon name="arrow-right" class="size-4" />
                        </flux:button>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
