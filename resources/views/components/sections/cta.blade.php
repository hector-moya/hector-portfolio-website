@props(['section', 'assets' => collect()])

@php $data = $section['data']; @endphp

<div class="bg-gradient-to-r from-teal-900/90 to-green-900/90 text-white">
    <div class="mx-auto max-w-4xl px-6 py-16 text-center sm:py-24 lg:px-8">
        @if (!empty($data['title']))
            <flux:heading class="!text-3xl font-bold text-white sm:!text-4xl" level="2">
                {{ $data['title'] }}
            </flux:heading>
        @endif

        @if (!empty($data['content']))
            <flux:text class="mt-6 text-lg leading-8 text-teal-100">
                {!! nl2br(e($data['content'])) !!}
            </flux:text>
        @endif

        @if (!empty($data['cta_text']))
            <div class="mt-10">
                <flux:button variant="primary" href="{{ $data['cta_url'] ?? '#' }}" class="shadow-xl">
                    {{ $data['cta_text'] }}
                </flux:button>
            </div>
        @endif
    </div>
</div>
