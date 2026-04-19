@props(['section', 'assets' => collect()])

@php
    $data = $section['data'];
    $imageIds = $data['images'] ?? [];
    $images = collect($imageIds)->map(fn ($id) => $assets->find($id))->filter();
@endphp

@if ($images->isNotEmpty())
    <div class="relative overflow-hidden bg-zinc-50 py-16 dark:bg-zinc-800/50 sm:py-24">
        {{-- Decorative accent --}}
        <div class="pointer-events-none absolute -bottom-20 -right-20 h-72 w-72 rounded-full bg-green-100/50 blur-3xl dark:bg-green-900/15" aria-hidden="true"></div>
        <div class="relative mx-auto max-w-7xl px-6 lg:px-8">
            @if (!empty($data['title']))
                <div data-animate class="mx-auto max-w-2xl text-center">
                    <flux:heading class="mb-10 text-3xl! font-bold sm:text-4xl!" level="2">
                        {{ $data['title'] }}
                    </flux:heading>
                </div>
            @endif

            <div @class([
                'grid gap-4',
                'grid-cols-2 sm:grid-cols-3' => $images->count() > 2,
                'grid-cols-2'               => $images->count() === 2,
                'grid-cols-1'               => $images->count() === 1,
            ])>
                @foreach ($images as $i => $image)
                    @php $delay = min(($i + 1) * 100, 500); @endphp
                    <div
                        data-animate
                        data-delay="{{ $delay }}"
                        class="group overflow-hidden rounded-2xl shadow-md ring-1 ring-zinc-900/5 dark:ring-white/5"
                    >
                        <img
                            src="{{ $image->url }}"
                            alt="{{ $image->alt_text }}"
                            class="aspect-4/3 w-full object-cover transition-transform duration-500 group-hover:scale-105"
                        />
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif
