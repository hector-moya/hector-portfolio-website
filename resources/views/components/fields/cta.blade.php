@props(['section', 'assets' => collect()])

@php $data = $section['data']; @endphp

<div class="relative overflow-hidden bg-linear-to-br from-teal-700 via-teal-800 to-green-800 dark:from-teal-950 dark:via-zinc-900 dark:to-green-950">
    {{-- Decorative glow --}}
    <div class="pointer-events-none absolute inset-0 overflow-hidden" aria-hidden="true">
        <div class="absolute left-1/2 top-1/2 h-96 w-96 -translate-x-1/2 -translate-y-1/2 rounded-full bg-teal-300/20 blur-3xl dark:bg-teal-500/20"></div>
    </div>

    <div class="relative mx-auto max-w-4xl px-6 py-20 text-center sm:py-28 lg:px-8">
        @if (!empty($data['title']))
            <div data-animate>
                <flux:heading class="text-3xl! font-bold text-white sm:text-4xl! lg:text-5xl!" level="2">
                    {{ $data['title'] }}
                </flux:heading>
            </div>
        @endif

        @if (!empty($data['content']))
            <div data-animate data-delay="100">
                <flux:text class="mx-auto mt-6 max-w-2xl text-lg leading-8 text-white/80 dark:text-teal-100/80">
                    {!! nl2br(e($data['content'])) !!}
                </flux:text>
            </div>
        @endif

        @if (!empty($data['cta_text']))
            <div data-animate data-delay="200" class="mt-10">
                <flux:button variant="primary" href="{{ $data['cta_url'] ?? '#' }}" class="shadow-xl shadow-teal-900/50">
                    {{ $data['cta_text'] }}
                    <flux:icon name="arrow-right" class="size-4" />
                </flux:button>
            </div>
        @endif
    </div>
</div>
