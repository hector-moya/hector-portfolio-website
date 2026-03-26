<div>
    @if ($entry && !empty($entry->layout))
        @foreach ($entry->layout as $section)
            <x-dynamic-component
                :component="'sections.' . str_replace('_', '-', $section['type'])"
                :section="$section"
                :assets="$assets"
                wire:key="section-{{ $section['_id'] }}"
            />
        @endforeach
    @elseif ($entry)
        {{-- Legacy: entry with blueprint elements but no layout sections yet --}}
        <x-themes.greenpeace>
            <div class="mx-auto max-w-2xl gap-x-20 lg:mx-0 lg:flex lg:max-w-none lg:items-center">
                <div class="relative w-full space-y-8 lg:shrink-0 xl:max-w-xl">
                    <flux:heading class="leading-16 animate-slide-in-from-top !text-6xl font-bold opacity-0" level="1">
                        {{ $entry->elements->firstWhere('handle', 'hero_title')?->value ?? 'Welcome' }}
                    </flux:heading>
                    <flux:heading size="xl" class="animate-slide-in-from-left opacity-0">
                        {{ $entry->elements->firstWhere('handle', 'hero_subtitle')?->value ?? 'Your subtitle here' }}
                    </flux:heading>
                    <flux:text size="xl" class="animate-slide-in-from-left text-base leading-10 opacity-0">
                        {!! nl2br(e($entry->elements->firstWhere('handle', 'content')?->value ?? 'Your content here')) !!}
                    </flux:text>
                    <div class="animate-slide-in-from-right mt-10 flex items-center gap-x-6 opacity-0">
                        <flux:button variant="primary" href="{{ $entry->elements->firstWhere('handle', 'cta_url_primary')?->value ?? '#' }}" class="shadow-xl">
                            {{ $entry->elements->firstWhere('handle', 'cta_text_primary')?->value }}
                        </flux:button>
                        <flux:button href="{{ $entry->elements->firstWhere('handle', 'cta_url_secondary')?->value ?? '#' }}" class="shadow-xl">
                            {{ $entry->elements->firstWhere('handle', 'cta_text_secondary')?->value }}
                        </flux:button>
                    </div>
                </div>
            </div>
        </x-themes.greenpeace>
    @else
        <x-themes.greenpeace>
            <div class="mx-auto max-w-7xl space-y-8 px-4 py-24 text-center sm:px-6 lg:px-8">
                <flux:heading class="!text-4xl">{{ __('Welcome') }}</flux:heading>
                <flux:text>{{ __('Content coming soon...') }}</flux:text>
            </div>
        </x-themes.greenpeace>
    @endif
</div>
