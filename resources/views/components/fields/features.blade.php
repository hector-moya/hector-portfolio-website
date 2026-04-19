@props(['section', 'assets' => collect()])

@php
    $data = $section['data'];
    $items = $data['items'] ?? [];
@endphp

@if (!empty($data['title']) || !empty($items))
    <div class="relative overflow-hidden bg-white py-16 dark:bg-zinc-900 sm:py-24">
        {{-- Decorative accent --}}
        <div class="pointer-events-none absolute -bottom-20 -left-20 h-72 w-72 rounded-full bg-green-100/60 blur-3xl dark:bg-green-900/20" aria-hidden="true"></div>
        <div class="relative mx-auto max-w-7xl px-6 lg:px-8">
            @if (!empty($data['title']))
                <div data-animate class="mx-auto max-w-2xl text-center">
                    <flux:heading class="text-3xl! font-bold sm:text-4xl!" level="2">
                        {{ $data['title'] }}
                    </flux:heading>
                </div>
            @endif

            @if (!empty($items))
                <div @class([
                    'mt-12 grid gap-6',
                    'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3' => count($items) > 2,
                    'grid-cols-1 sm:grid-cols-2'               => count($items) === 2,
                    'mx-auto max-w-md grid-cols-1'             => count($items) === 1,
                ])>
                    @foreach ($items as $i => $item)
                        @php $delay = min(($i + 1) * 100, 500); @endphp
                        <div
                            data-animate
                            data-delay="{{ $delay }}"
                            class="group flex flex-col gap-4 rounded-2xl border border-zinc-200 bg-zinc-50/50 p-6 transition-all duration-300 hover:-translate-y-1 hover:shadow-lg dark:border-zinc-700/60 dark:bg-zinc-800/50 dark:hover:border-teal-700/50 dark:hover:shadow-teal-950/50"
                        >
                            @if (!empty($item['icon']))
                                @php $iconName = Str::kebab($item['icon']); @endphp
                                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-linear-to-br from-teal-500 to-green-600 text-white shadow-sm shadow-teal-500/30">
                                    @if (view()->exists('flux::icon.' . $iconName))
                                        <flux:icon :name="$iconName" class="size-5" />
                                    @else
                                        <flux:icon name="sparkles" class="size-5" />
                                    @endif
                                </div>
                            @endif

                            @if (!empty($item['item_title']))
                                <flux:heading size="md" class="font-semibold">
                                    {{ $item['item_title'] }}
                                </flux:heading>
                            @endif

                            @if (!empty($item['item_description']))
                                <flux:text class="text-zinc-500 dark:text-zinc-300">
                                    {{ $item['item_description'] }}
                                </flux:text>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endif
