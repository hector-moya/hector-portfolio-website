@props(['section', 'assets' => collect()])

@php
    $data = $section['data'];
    $imageIds = $data['images'] ?? [];
    $images = collect($imageIds)->map(fn ($id) => $assets->find($id))->filter();
@endphp

@if ($images->isNotEmpty())
    <div class="bg-zinc-50 py-16 dark:bg-zinc-800 sm:py-24">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            @if (!empty($data['title']))
                <flux:heading class="mb-10 text-center !text-3xl font-bold sm:!text-4xl" level="2">
                    {{ $data['title'] }}
                </flux:heading>
            @endif

            <div @class([
                'grid gap-4',
                'grid-cols-2 sm:grid-cols-3' => $images->count() > 2,
                'grid-cols-2' => $images->count() === 2,
                'grid-cols-1' => $images->count() === 1,
            ])>
                @foreach ($images as $image)
                    <div class="overflow-hidden rounded-xl shadow-md ring-1 ring-zinc-900/5 dark:ring-white/5">
                        <img
                            src="{{ $image->url }}"
                            alt="{{ $image->alt_text }}"
                            class="aspect-[4/3] w-full object-cover transition-transform duration-300 hover:scale-105"
                        />
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif
