@props(['collection', 'sections' => [], 'assets', 'childEntries', 'childTemplate' => 'card-grid', 'theme' => 'greenpeace'])

{{-- Page builder sections (each handles its own SP background/styling) --}}
@foreach($sections as $section)
    <x-dynamic-component
        :component="'fields.' . str_replace('_', '-', $section['type'])"
        :section="$section"
        :assets="$assets"
        wire:key="section-{{ $section['_id'] }}"
    />
@endforeach

{{-- Child entries listing --}}
@if ($childEntries->isNotEmpty())
    <div class="relative overflow-hidden py-16 sm:py-24" style="background: var(--sp-bg);">

        {{-- Decorative accent blob --}}
        <div class="pointer-events-none absolute -right-24 -top-24 h-80 w-80 rounded-full opacity-20 blur-3xl dark:opacity-10" style="background: var(--sp-bio);" aria-hidden="true"></div>

        @if (count($sections) > 0)
            <div class="mb-16 mx-6 lg:mx-auto lg:max-w-7xl lg:px-8" style="height: 1px; background: linear-gradient(90deg, transparent, var(--sp-border), transparent);"></div>
        @endif

        <div class="relative z-10 mx-auto max-w-7xl px-6 lg:px-8">

            {{-- Section label --}}
            <div data-animate class="mb-10 text-center">
                <div class="mb-2 text-[0.68rem] font-semibold uppercase tracking-[0.28em]" style="color: var(--sp-bio); text-shadow: 0 0 12px var(--sp-glow-b);">
                    {{ $collection->name }}
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($childEntries as $i => $entry)
                    @php
                        $featuredImage = $entry->elements->firstWhere('handle', 'featured_image')?->getElementValue();
                        $excerpt       = $entry->elements->first(fn ($el) => in_array($el->field?->type, ['textarea', 'text']) && $el->getElementValue())?->getElementValue();
                        $accents       = [['--sp-solar', '--sp-glow-s'], ['--sp-bio', '--sp-glow-b'], ['--sp-leaf', '--sp-glow-l']];
                        [$accentVar]   = $accents[$i % 3];
                        $delay         = min(($i + 1) * 100, 500);
                    @endphp
                    <div
                        data-animate
                        data-delay="{{ $delay }}"
                        class="group flex flex-col overflow-hidden rounded-xl border transition-all duration-300 hover:-translate-y-1.5"
                        style="background: var(--sp-bg-card); border-color: var(--sp-border);"
                        onmouseover="this.style.borderColor='oklch(from var({{ $accentVar }}) l c h / 0.5)'; this.style.boxShadow='0 0 28px oklch(from var({{ $accentVar }}) l c h / 0.15), 0 10px 36px oklch(0 0 0 / 0.2)';"
                        onmouseout="this.style.borderColor='var(--sp-border)'; this.style.boxShadow='none';"
                    >
                        <div class="absolute left-[10%] right-[10%] top-0 h-px opacity-0 transition-opacity duration-300 group-hover:opacity-100" style="background: linear-gradient(90deg, transparent, var({{ $accentVar }}), transparent);"></div>

                        @if ($featuredImage)
                            <div class="overflow-hidden">
                                <img
                                    src="{{ $featuredImage }}"
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
                                    <span>•</span>
                                @endif
                                @if ($entry->published_at)
                                    <span>{{ $entry->published_at->format('M d, Y') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

@elseif (count($sections) === 0)
    <div class="relative overflow-hidden py-32" style="background: var(--sp-bg);">
        <div class="mx-auto max-w-3xl px-6 text-center">
            <h1 class="text-[clamp(1.8rem,3.5vw,2.8rem)] font-semibold" style="font-family: 'Cinzel', serif; color: var(--sp-fg);">
                {{ $collection->name }}
            </h1>
            <p class="mt-4 text-sm" style="color: var(--sp-muted);">{{ __('Content coming soon.') }}</p>
        </div>
    </div>
@endif
