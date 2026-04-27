@props(['section', 'assets' => collect()])

@php
    $data  = $section['data'];
    $cards = collect($data['cards'] ?? [])->filter(fn ($card) => !empty($card['title']) || !empty($card['image']));
@endphp

@if (!empty($data['title']) || !empty($data['subtitle']) || $cards->isNotEmpty())
    <div class="relative overflow-hidden py-16 sm:py-24" style="background: var(--sp-bg-mid);">

        {{-- Decorative accent blob --}}
        <div class="pointer-events-none absolute -right-20 -top-20 h-72 w-72 rounded-full opacity-20 blur-3xl dark:opacity-10" style="background: var(--sp-solar);" aria-hidden="true"></div>

        <div class="relative z-10 mx-auto max-w-7xl px-6 lg:px-8">
            @if (!empty($data['title']) || !empty($data['subtitle']))
                <div data-animate class="mx-auto max-w-2xl text-center">
                    @if (!empty($data['title']))
                        <h2 class="text-[clamp(1.8rem,3vw,2.6rem)] font-semibold" style="font-family: 'Cinzel', serif; color: var(--sp-fg);">
                            {{ $data['title'] }}
                        </h2>
                    @endif
                    @if (!empty($data['subtitle']))
                        <p class="mt-4 text-base leading-7" style="color: var(--sp-muted);">
                            {{ $data['subtitle'] }}
                        </p>
                    @endif
                </div>
            @endif

            @if ($cards->isNotEmpty())
                <div @class([
                    'mt-12 grid gap-6',
                    'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3' => $cards->count() !== 2,
                    'grid-cols-1 sm:grid-cols-2'               => $cards->count() === 2,
                ])>
                    @foreach ($cards as $i => $card)
                        @php
                            $cardAsset = ($card['image'] ?? null) ? $assets->find($card['image']) : null;
                            $delay     = min(($i + 1) * 100, 500);
                        @endphp
                        <div
                            data-animate
                            data-delay="{{ $delay }}"
                            class="group flex flex-col overflow-hidden rounded-xl border transition-all duration-300 hover:-translate-y-1"
                            style="background: var(--sp-bg-card); border-color: var(--sp-border);"
                            onmouseover="this.style.boxShadow='0 0 24px oklch(from var(--sp-bio) l c h / 0.14), 0 8px 32px oklch(0 0 0 / 0.2)'; this.style.borderColor='oklch(from var(--sp-bio) l c h / 0.4)';"
                            onmouseout="this.style.boxShadow='none'; this.style.borderColor='var(--sp-border)';"
                        >
                            @if ($cardAsset)
                                <div class="overflow-hidden">
                                    <img
                                        src="{{ $cardAsset->url }}"
                                        alt="{{ $cardAsset->alt_text ?? ($card['title'] ?? '') }}"
                                        class="aspect-video w-full object-cover transition-transform duration-500 group-hover:scale-105"
                                        style="filter: saturate(0.88);"
                                    />
                                </div>
                            @endif

                            <div class="flex flex-1 flex-col gap-3 p-6">
                                @if (!empty($card['title']))
                                    <h3 class="text-base font-semibold leading-snug" style="font-family: 'Cinzel', serif; color: var(--sp-fg);">
                                        {{ $card['title'] }}
                                    </h3>
                                @endif

                                @if (!empty($card['excerpt']))
                                    <p class="flex-1 text-sm leading-7" style="color: var(--sp-muted);">
                                        {{ $card['excerpt'] }}
                                    </p>
                                @endif

                                @if (!empty($card['link']))
                                    <div class="mt-2">
                                        <a
                                            href="{{ $card['link'] }}"
                                            class="inline-flex items-center gap-1.5 text-xs font-semibold uppercase tracking-widest transition-opacity hover:opacity-80"
                                            style="color: var(--sp-bio);"
                                        >
                                            {{ __('Read more') }}
                                            <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <div class="relative z-10" style="height: 1px; background: linear-gradient(90deg, transparent, var(--sp-border), transparent);"></div>
@endif
