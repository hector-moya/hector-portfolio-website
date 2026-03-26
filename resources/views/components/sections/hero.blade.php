@props(['section', 'assets' => collect()])

@php
    $data = $section['data'];
    $bgImage = isset($data['bg_image']) && $data['bg_image'] ? $assets->find($data['bg_image']) : null;
@endphp

<div class="relative bg-gradient-to-r from-teal-900/80 to-green-900/80 text-white">
    @if ($bgImage)
        <div class="absolute inset-0 -z-10">
            <img src="{{ $bgImage->url }}" alt="{{ $bgImage->alt_text }}" class="h-full w-full object-cover opacity-30" />
        </div>
    @endif

    <div class="relative isolate overflow-hidden">
        <svg aria-hidden="true" class="absolute inset-x-0 top-0 -z-10 h-full w-full stroke-white/10" viewBox="0 0 800 600" preserveAspectRatio="none">
            <defs>
                <pattern id="hero-grid-{{ $section['_id'] }}" width="100" height="100" x="50%" y="-1" patternUnits="userSpaceOnUse">
                    <line x1="0" y1="20" x2="0" y2="80" stroke="white" stroke-width="0.5" opacity="0.3" />
                    <line x1="20" y1="0" x2="80" y2="0" stroke="white" stroke-width="0.5" opacity="0.3" />
                </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#hero-grid-{{ $section['_id'] }})" />
        </svg>

        <div class="mx-auto max-w-7xl px-6 py-24 sm:py-32 lg:px-8">
            <div class="mx-auto max-w-2xl lg:mx-0 lg:max-w-none">
                @if (!empty($data['title']))
                    <flux:heading class="!text-5xl font-bold leading-tight text-white sm:!text-6xl" level="1">
                        {{ $data['title'] }}
                    </flux:heading>
                @endif

                @if (!empty($data['subtitle']))
                    <flux:heading size="xl" class="mt-4 text-teal-200">
                        {{ $data['subtitle'] }}
                    </flux:heading>
                @endif

                @if (!empty($data['content']))
                    <flux:text size="xl" class="mt-6 text-lg leading-8 text-zinc-300">
                        {!! nl2br(e($data['content'])) !!}
                    </flux:text>
                @endif

                @if (!empty($data['cta_text']) || !empty($data['secondary_cta_text']))
                    <div class="mt-10 flex flex-wrap items-center gap-4">
                        @if (!empty($data['cta_text']))
                            <flux:button variant="primary" href="{{ $data['cta_url'] ?? '#' }}" class="shadow-xl">
                                {{ $data['cta_text'] }}
                            </flux:button>
                        @endif
                        @if (!empty($data['secondary_cta_text']))
                            <flux:button href="{{ $data['secondary_cta_url'] ?? '#' }}" class="shadow-xl !text-white">
                                {{ $data['secondary_cta_text'] }}
                            </flux:button>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
