@props(['section', 'assets' => collect()])

@php
    $data = $section['data'];
    $items = $data['items'] ?? [];
@endphp

@if (!empty($data['title']) || !empty($items))
    <div class="bg-white py-16 dark:bg-zinc-900 sm:py-24">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            @if (!empty($data['title']))
                <flux:heading class="mb-12 text-center !text-3xl font-bold sm:!text-4xl" level="2">
                    {{ $data['title'] }}
                </flux:heading>
            @endif

            @if (!empty($items))
                <div @class([
                    'grid gap-8',
                    'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3' => count($items) > 2,
                    'grid-cols-1 sm:grid-cols-2' => count($items) === 2,
                    'grid-cols-1 max-w-md mx-auto' => count($items) === 1,
                ])>
                    @foreach ($items as $item)
                        <div class="flex flex-col gap-4 rounded-2xl border border-zinc-200 p-6 dark:border-zinc-700">
                            @if (!empty($item['icon']))
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-teal-600 text-white">
                                    <flux:icon :name="$item['icon']" class="size-6" />
                                </div>
                            @endif

                            @if (!empty($item['item_title']))
                                <flux:heading size="md" class="font-semibold">
                                    {{ $item['item_title'] }}
                                </flux:heading>
                            @endif

                            @if (!empty($item['item_description']))
                                <flux:text class="text-zinc-600 dark:text-zinc-400">
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
