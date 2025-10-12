<div>
    @if ($entry)
        {{-- Hero Section --}}
        <div class="relative bg-gradient-to-r from-indigo-600 to-purple-600 text-white">
            <div class="mx-auto max-w-7xl px-4 py-24 sm:px-6 lg:px-8">
                <div class="text-center">
                    <flux:heading class="text-4xl font-bold tracking-tight sm:text-5xl md:text-6xl">
                        {{ $entry->elements->firstWhere('handle', 'hero_title')?->value ?? 'Welcome' }}
                    </flux:heading>
                    @if ($subtitle = $entry->elements->firstWhere('handle', 'hero_subtitle')?->value)
                        <flux:text class="mx-auto mt-6 max-w-3xl text-xl">
                            {{ $subtitle }}
                        </flux:text>
                        <flux:text class="mx-auto mt-6 max-w-3xl text-xl">
                            {!! nl2br(e($entry->elements->firstWhere('handle', 'content')?->value ?? '')) !!}
                        </flux:text>
                    @endif
                    <div class="flex justify-center space-x-4">
                        <div class="mt-10">
                            <flux:button href="{{ $entry->elements->firstWhere('handle', 'cta_url')?->value ?? '#' }}" class="inline-block rounded-lg bg-white px-8 py-3 font-semibold text-indigo-600 transition hover:bg-zinc-100">
                                {{ $entry->elements->firstWhere('handle', 'cta_text')?->value }}
                            </flux:button>
                        </div>
                        <div class="mt-10">
                            <flux:button href="{{ $entry->elements->firstWhere('handle', 'cta_url_1')?->value ?? '#' }}" class="inline-block rounded-lg bg-white px-8 py-3 font-semibold text-indigo-600 transition hover:bg-zinc-100">
                                {{ $entry->elements->firstWhere('handle', 'cta_text_1')?->value }}
                            </flux:button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="mx-auto max-w-7xl px-4 py-24 text-center sm:px-6 lg:px-8">
            <h1 class="text-4xl font-bold text-zinc-900 dark:text-white">Welcome</h1>
            <p class="mt-4 text-zinc-600 dark:text-zinc-400">Content coming soon...</p>
        </div>
    @endif
</div>
