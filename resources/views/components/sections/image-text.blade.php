@props(['section', 'assets' => collect()])

@php
    $data = $section['data'];
    $image = isset($data['image']) && $data['image'] ? $assets->find($data['image']) : null;
    $imageRight = ($data['image_position'] ?? 'left') === 'right';
@endphp

<div class="bg-white py-16 dark:bg-zinc-900 sm:py-24">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
        <div @class([
            'flex flex-col gap-12 lg:flex-row lg:items-center',
            'lg:flex-row-reverse' => $imageRight,
        ])>
            {{-- Image column --}}
            @if ($image)
                <div data-animate class="lg:w-1/2">
                    <img
                        src="{{ $image->url }}"
                        alt="{{ $image->alt_text }}"
                        class="w-full rounded-2xl object-cover shadow-2xl ring-1 ring-zinc-900/10 dark:ring-white/10"
                    />
                </div>
            @endif

            {{-- Text column --}}
            <div data-animate data-delay="100" class="{{ $image ? 'lg:w-1/2' : 'w-full' }} space-y-6">
                @if (!empty($data['title']))
                    <flux:heading class="text-3xl! font-bold sm:text-4xl!" level="2">
                        {{ $data['title'] }}
                    </flux:heading>
                @endif

                @if (!empty($data['content']))
                    <flux:text class="text-lg leading-8 text-zinc-500 dark:text-zinc-400">
                        {!! nl2br(e($data['content'])) !!}
                    </flux:text>
                @endif

                @if (!empty($data['cta_text']))
                    <div>
                        <flux:button variant="primary" href="{{ $data['cta_url'] ?? '#' }}">
                            {{ $data['cta_text'] }}
                            <flux:icon name="arrow-right" class="size-4" />
                        </flux:button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
