@props(['collection', 'entries', 'theme' => 'greenpeace'])

<div class="relative overflow-hidden py-20 sm:py-28" style="background: var(--sp-bg-mid);">

    {{-- Decorative accent blob --}}
    <div class="pointer-events-none absolute -left-24 -top-24 h-80 w-80 rounded-full opacity-20 blur-3xl dark:opacity-10" style="background: var(--sp-bio);" aria-hidden="true"></div>

    <div class="relative z-10 mx-auto max-w-4xl px-6 lg:px-8">

        {{-- Section header --}}
        <div data-animate class="mb-12">
            <div class="mb-2 text-[0.68rem] font-semibold uppercase tracking-[0.28em]" style="color: var(--sp-leaf); text-shadow: 0 0 12px var(--sp-glow-l);">
                {{ __('Collection') }}
            </div>
            <h1 class="text-[clamp(1.8rem,3.5vw,2.8rem)] font-semibold" style="font-family: 'Cinzel', serif; color: var(--sp-fg);">
                {{ $collection->name }}
            </h1>
            @if ($collection->description)
                <p class="mt-3 text-base leading-7" style="color: var(--sp-muted);">
                    {{ $collection->description }}
                </p>
            @endif
        </div>

        {{-- Entry list --}}
        <div class="flex flex-col gap-4">
            @forelse($entries as $i => $entry)
                @php
                    $rawImage   = $entry->elements->firstWhere('handle', 'featured_image')?->getElementValue();
                    $image      = is_string($rawImage) ? $rawImage : null;
                    $rawExcerpt = $entry->elements->first(fn ($el) => in_array($el->field?->type, ['textarea', 'text', 'text_block']) && is_string($el->getElementValue()))?->getElementValue();
                    $excerpt    = is_string($rawExcerpt) ? $rawExcerpt : null;
                    $delay      = min(($i + 1) * 100, 500);
                @endphp
                <div
                    data-animate
                    data-delay="{{ $delay }}"
                    class="group flex items-start gap-5 rounded-xl border p-5 transition-all duration-300 hover:-translate-y-0.5"
                    style="background: var(--sp-bg-card); border-color: var(--sp-border);"
                    onmouseover="this.style.borderColor='oklch(from var(--sp-bio) l c h / 0.45)'; this.style.boxShadow='0 0 20px oklch(from var(--sp-bio) l c h / 0.12), 0 4px 20px oklch(0 0 0 / 0.15)';"
                    onmouseout="this.style.borderColor='var(--sp-border)'; this.style.boxShadow='none';"
                >
                    @if ($image)
                        <div class="shrink-0 overflow-hidden rounded-lg">
                            <img
                                src="{{ $image }}"
                                alt="{{ $entry->title }}"
                                class="h-20 w-20 object-cover transition-transform duration-300 group-hover:scale-105"
                                style="filter: saturate(0.88);"
                            />
                        </div>
                    @endif

                    <div class="min-w-0 flex-1">
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
                            <p class="mt-1.5 line-clamp-2 text-sm leading-6" style="color: var(--sp-muted);">
                                {{ $excerpt }}
                            </p>
                        @endif

                        <div class="mt-2 flex items-center gap-2 text-xs tracking-wide" style="color: var(--sp-muted);">
                            @if ($entry->author)
                                <span>{{ $entry->author->name }}</span>
                                <span>•</span>
                            @endif
                            @if ($entry->published_at)
                                <span>{{ $entry->published_at->format('M d, Y') }}</span>
                            @endif
                        </div>
                    </div>

                    {{-- Arrow indicator --}}
                    <div class="shrink-0 self-center opacity-0 transition-opacity duration-200 group-hover:opacity-100" style="color: var(--sp-bio);">
                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </div>
                </div>
            @empty
                <div class="py-16 text-center">
                    <p class="text-sm" style="color: var(--sp-muted);">{{ __('No entries available yet.') }}</p>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if ($entries->hasPages())
            <div class="mt-12" style="color: var(--sp-muted);">
                {{ $entries->links() }}
            </div>
        @endif

    </div>
</div>
