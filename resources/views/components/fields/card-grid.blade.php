@props(['section', 'assets' => collect()])

@php
    $data = $section['data'];
    $cards = collect($data['cards'] ?? [])->filter(fn ($card) => !empty($card['title']) || !empty($card['image']));
@endphp

@if (!empty($data['title']) || !empty($data['subtitle']) || $cards->isNotEmpty())
    <div class="relative overflow-hidden bg-white py-16 dark:bg-zinc-900 sm:py-24">
        {{-- Decorative accent --}}
        <div class="pointer-events-none absolute -right-20 -top-20 h-72 w-72 rounded-full bg-teal-100/60 blur-3xl dark:bg-teal-900/20" aria-hidden="true"></div>
        <div class="relative mx-auto max-w-7xl px-6 lg:px-8">
            @if (!empty($data['title']) || !empty($data['subtitle']))
                <div data-animate class="mx-auto max-w-2xl text-center">
                    @if (!empty($data['title']))
                        <flux:heading class="text-3xl! font-bold sm:text-4xl!" level="2">
                            {{ $data['title'] }}
                        </flux:heading>
                    @endif
                    @if (!empty($data['subtitle']))
                        <flux:text size="lg" class="mt-4 text-zinc-500 dark:text-zinc-300">
                            {{ $data['subtitle'] }}
                        </flux:text>
                    @endif
                </div>
            @endif

            @if ($cards->isNotEmpty())
                <div @class([
                    'mt-12 grid gap-8',
                    'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3' => $cards->count() !== 2,
                    'grid-cols-1 sm:grid-cols-2'               => $cards->count() === 2,
                ])>
                    @foreach ($cards as $i => $card)
                        @php
                            $cardAsset = ($card['image'] ?? null) ? $assets->find($card['image']) : null;
                            $delay = min(($i + 1) * 100, 500);
                        @endphp
                        <div
                            data-animate
                            data-delay="{{ $delay }}"
                            class="group flex flex-col overflow-hidden rounded-2xl border border-zinc-200 bg-zinc-50/50 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg dark:border-zinc-700/60 dark:bg-zinc-800/50 dark:hover:border-teal-700/50 dark:hover:shadow-teal-950/50"
                        >
                            @if ($cardAsset)
                                <div class="overflow-hidden">
                                    <img
                                        src="{{ $cardAsset->url }}"
                                        alt="{{ $cardAsset->alt_text ?? ($card['title'] ?? '') }}"
                                        class="aspect-video w-full object-cover transition-transform duration-500 group-hover:scale-105"
                                    />
                                </div>
                            @endif

                            <div class="flex flex-1 flex-col gap-3 p-6">
                                @if (!empty($card['title']))
                                    <flux:heading size="md" class="font-semibold leading-snug">
                                        {{ $card['title'] }}
                                    </flux:heading>
                                @endif

                                @if (!empty($card['excerpt']))
                                    <flux:text class="flex-1 text-zinc-500 dark:text-zinc-300">
                                        {{ $card['excerpt'] }}
                                    </flux:text>
                                @endif

                                @if (!empty($card['link']))
                                    <div class="mt-2">
                                        <flux:button variant="ghost" size="sm" href="{{ $card['link'] }}" class="px-0 text-teal-600 hover:text-teal-700 dark:text-teal-400 dark:hover:text-teal-300">
                                            {{ __('Read more') }}
                                            <flux:icon name="arrow-right" class="size-4" />
                                        </flux:button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endif
