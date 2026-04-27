@props(['collection', 'entries', 'theme' => 'greenpeace'])

@php
    $featured = $entries->first();
    $rest     = $entries->slice(1);
    $featuredImage = null;
    $featuredExcerpt = null;
    if ($featured) {
        $raw = $featured->elements->firstWhere('handle', 'featured_image')?->getElementValue();
        $featuredImage = is_string($raw) ? $raw : null;
        $raw = $featured->elements->first(fn ($el) => in_array($el->field?->type, ['textarea', 'text', 'text_block']) && is_string($el->getElementValue()))?->getElementValue();
        $featuredExcerpt = is_string($raw) ? $raw : null;
    }
@endphp

<div class="relative overflow-hidden py-20 sm:py-28" style="background: var(--sp-bg);">

    {{-- Decorative blobs --}}
    <div class="pointer-events-none absolute -left-24 top-0 h-96 w-96 rounded-full opacity-20 blur-3xl dark:opacity-10" style="background: var(--sp-solar);" aria-hidden="true"></div>
    <div class="pointer-events-none absolute -right-24 bottom-0 h-80 w-80 rounded-full opacity-20 blur-3xl dark:opacity-10" style="background: var(--sp-bio);" aria-hidden="true"></div>

    <div class="relative z-10 mx-auto max-w-7xl px-6 lg:px-8">

        {{-- Section header --}}
        <div data-animate class="mb-12">
            <div class="mb-2 text-[0.68rem] font-semibold uppercase tracking-[0.28em]" style="color: var(--sp-solar); text-shadow: 0 0 12px var(--sp-glow-s);">
                {{ __('Collection') }}
            </div>
            <h1 class="text-[clamp(1.8rem,3.5vw,2.8rem)] font-semibold" style="font-family: 'Cinzel', serif; color: var(--sp-fg);">
                {{ $collection->name }}
            </h1>
            @if ($collection->description)
                <p class="mt-3 max-w-xl text-base leading-7" style="color: var(--sp-muted);">
                    {{ $collection->description }}
                </p>
            @endif
        </div>

        {{-- Featured entry --}}
        @if ($featured)
            <div
                data-animate
                class="group mb-10 overflow-hidden rounded-xl border transition-all duration-300 hover:-translate-y-1"
                style="background: var(--sp-bg-card); border-color: var(--sp-border);"
                onmouseover="this.style.borderColor='oklch(from var(--sp-solar) l c h / 0.45)'; this.style.boxShadow='0 0 36px oklch(from var(--sp-solar) l c h / 0.14), 0 12px 48px oklch(0 0 0 / 0.25)';"
                onmouseout="this.style.borderColor='var(--sp-border)'; this.style.boxShadow='none';"
            >
                <div class="lg:flex">
                    @if ($featuredImage)
                        <div class="shrink-0 overflow-hidden lg:w-1/2">
                            <img
                                src="{{ $featuredImage }}"
                                alt="{{ $featured->title }}"
                                class="h-64 w-full object-cover transition-transform duration-500 group-hover:scale-105 lg:h-full"
                                style="filter: saturate(0.88);"
                            />
                        </div>
                    @endif
                    <div class="flex flex-1 flex-col justify-center gap-4 p-8 lg:p-10">
                        <div class="text-[0.65rem] font-semibold uppercase tracking-[0.25em]" style="color: var(--sp-solar);">
                            {{ __('Featured') }}
                        </div>
                        <h2 class="text-[clamp(1.6rem,3vw,2.4rem)] font-semibold leading-snug" style="font-family: 'Cinzel', serif; color: var(--sp-fg);">
                            <a
                                href="{{ route('entry.show', [$collection->slug, $featured->slug]) }}"
                                wire:navigate
                                class="transition-opacity hover:opacity-70"
                            >
                                {{ $featured->title }}
                            </a>
                        </h2>
                        @if ($featuredExcerpt)
                            <p class="line-clamp-3 text-base leading-7" style="color: var(--sp-muted);">
                                {{ $featuredExcerpt }}
                            </p>
                        @endif
                        <div class="flex items-center gap-3">
                            <div class="text-xs tracking-wide" style="color: var(--sp-muted);">
                                @if ($featured->author)
                                    <span>{{ $featured->author->name }}</span>
                                    <span class="mx-1">•</span>
                                @endif
                                @if ($featured->published_at)
                                    <span>{{ $featured->published_at->format('M d, Y') }}</span>
                                @endif
                            </div>
                            <a
                                href="{{ route('entry.show', [$collection->slug, $featured->slug]) }}"
                                wire:navigate
                                class="ml-auto inline-flex items-center gap-1.5 text-xs font-semibold uppercase tracking-widest transition-opacity hover:opacity-70"
                                style="color: var(--sp-solar);"
                            >
                                {{ __('Read') }}
                                <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Divider --}}
        @if ($rest->isNotEmpty())
            <div class="mb-10" style="height: 1px; background: linear-gradient(90deg, transparent, var(--sp-border), transparent);"></div>

            {{-- Secondary grid --}}
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($rest as $i => $entry)
                    @php
                        $rawExcerpt = $entry->elements->first(fn ($el) => in_array($el->field?->type, ['textarea', 'text', 'text_block']) && is_string($el->getElementValue()))?->getElementValue();
                        $excerpt    = is_string($rawExcerpt) ? $rawExcerpt : null;
                        $accents    = [['--sp-bio', '--sp-glow-b'], ['--sp-leaf', '--sp-glow-l'], ['--sp-solar', '--sp-glow-s']];
                        [$accentVar] = $accents[$i % 3];
                        $delay      = min(($i + 1) * 100, 400);
                    @endphp
                    <div
                        data-animate
                        data-delay="{{ $delay }}"
                        class="group flex flex-col gap-3 rounded-xl border p-6 transition-all duration-300 hover:-translate-y-1"
                        style="background: var(--sp-bg-card); border-color: var(--sp-border);"
                        onmouseover="this.style.borderColor='oklch(from var({{ $accentVar }}) l c h / 0.45)'; this.style.boxShadow='0 0 20px oklch(from var({{ $accentVar }}) l c h / 0.12), 0 8px 28px oklch(0 0 0 / 0.18)';"
                        onmouseout="this.style.borderColor='var(--sp-border)'; this.style.boxShadow='none';"
                    >
                        <div class="absolute left-[10%] right-[10%] top-0 h-px opacity-0 transition-opacity duration-300 group-hover:opacity-100" style="background: linear-gradient(90deg, transparent, var({{ $accentVar }}), transparent);"></div>

                        <h2 class="text-sm font-semibold leading-snug" style="font-family: 'Cinzel', serif; color: var(--sp-fg);">
                            <a
                                href="{{ route('entry.show', [$collection->slug, $entry->slug]) }}"
                                wire:navigate
                                class="transition-opacity hover:opacity-70"
                            >
                                {{ $entry->title }}
                            </a>
                        </h2>

                        @if ($excerpt)
                            <p class="line-clamp-2 text-sm leading-6" style="color: var(--sp-muted);">
                                {{ $excerpt }}
                            </p>
                        @endif

                        @if ($entry->published_at)
                            <div class="mt-auto text-xs tracking-wide" style="color: var(--sp-muted);">
                                {{ $entry->published_at->format('M d, Y') }}
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Pagination --}}
        @if ($entries->hasPages())
            <div class="mt-14" style="color: var(--sp-muted);">
                {{ $entries->links() }}
            </div>
        @endif

    </div>
</div>
