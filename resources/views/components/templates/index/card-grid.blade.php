@props(['collection', 'entries', 'theme' => 'greenpeace'])

<div class="relative overflow-hidden py-20 sm:py-28" style="background: var(--sp-bg);">

    {{-- Decorative accent blob --}}
    <div class="pointer-events-none absolute -right-24 -top-24 h-96 w-96 rounded-full opacity-20 blur-3xl dark:opacity-10" style="background: var(--sp-solar);" aria-hidden="true"></div>

    <div class="relative z-10 mx-auto max-w-7xl px-6 lg:px-8">

        {{-- Section header --}}
        <div data-animate class="mb-14 text-center">
            <div class="mb-3 text-[0.68rem] font-semibold uppercase tracking-[0.28em]" style="color: var(--sp-bio); text-shadow: 0 0 12px var(--sp-glow-b);">
                {{ __('Collection') }}
            </div>
            <h1 class="text-[clamp(2rem,4vw,3.2rem)] font-semibold" style="font-family: 'Cinzel', serif; color: var(--sp-fg);">
                {{ $collection->name }}
            </h1>
            @if ($collection->description)
                <p class="mx-auto mt-4 max-w-xl text-base leading-7" style="color: var(--sp-muted);">
                    {{ $collection->description }}
                </p>
            @endif
        </div>

        {{-- Card grid --}}
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @forelse($entries as $i => $entry)
                @php
                    $rawImage    = $entry->elements->firstWhere('handle', 'featured_image')?->getElementValue();
                    $image       = is_string($rawImage) ? $rawImage : null;
                    $rawExcerpt  = $entry->elements->first(fn ($el) => in_array($el->field?->type, ['textarea', 'text', 'text_block']) && is_string($el->getElementValue()))?->getElementValue();
                    $excerpt     = is_string($rawExcerpt) ? $rawExcerpt : null;
                    $accents     = [['--sp-solar', '--sp-glow-s'], ['--sp-bio', '--sp-glow-b'], ['--sp-leaf', '--sp-glow-l']];
                    [$accentVar, $glowVar] = $accents[$i % 3];
                    $delay       = min(($i + 1) * 100, 500);
                @endphp
                <div
                    data-animate
                    data-delay="{{ $delay }}"
                    class="group flex flex-col overflow-hidden rounded-xl border transition-all duration-300 hover:-translate-y-1.5"
                    style="background: var(--sp-bg-card); border-color: var(--sp-border);"
                    onmouseover="this.style.borderColor='oklch(from var({{ $accentVar }}) l c h / 0.5)'; this.style.boxShadow='0 0 28px oklch(from var({{ $accentVar }}) l c h / 0.15), 0 10px 36px oklch(0 0 0 / 0.2)';"
                    onmouseout="this.style.borderColor='var(--sp-border)'; this.style.boxShadow='none';"
                >
                    {{-- Top-edge accent line on hover --}}
                    <div class="absolute left-[10%] right-[10%] top-0 h-px opacity-0 transition-opacity duration-300 group-hover:opacity-100" style="background: linear-gradient(90deg, transparent, var({{ $accentVar }}), transparent);"></div>

                    @if ($image)
                        <div class="overflow-hidden">
                            <img
                                src="{{ $image }}"
                                alt="{{ $entry->title }}"
                                class="aspect-video w-full object-cover transition-transform duration-500 group-hover:scale-105"
                                style="filter: saturate(0.88);"
                            />
                        </div>
                    @endif

                    <div class="flex flex-1 flex-col gap-3 p-6">
                        <h2 class="text-base font-semibold leading-snug" style="font-family: 'Cinzel', serif; color: var(--sp-fg);">
                            <a
                                href="{{ route('entry.show', [$collection->slug, $entry->slug]) }}"
                                wire:navigate
                                class="transition-opacity hover:opacity-70"
                            >
                                {{ $entry->title }}
                            </a>
                        </h2>

                        @if ($excerpt)
                            <p class="line-clamp-3 flex-1 text-sm leading-7" style="color: var(--sp-muted);">
                                {{ $excerpt }}
                            </p>
                        @endif

                        <div class="flex items-center gap-2 text-xs tracking-wide" style="color: var(--sp-muted);">
                            @if ($entry->author)
                                <span>{{ $entry->author->name }}</span>
                                <span style="color: var(--sp-border);">•</span>
                            @endif
                            @if ($entry->published_at)
                                <span>{{ $entry->published_at->format('M d, Y') }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-3 py-16 text-center">
                    <p class="text-sm" style="color: var(--sp-muted);">{{ __('No entries available yet.') }}</p>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if ($entries->hasPages())
            <div class="mt-14" style="color: var(--sp-muted);">
                {{ $entries->links() }}
            </div>
        @endif

    </div>
</div>
