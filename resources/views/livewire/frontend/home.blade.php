<div>
    @if($entry)
        {{-- Hero Section --}}
        <div class="relative bg-gradient-to-r from-indigo-600 to-purple-600 text-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
                <div class="text-center">
                    <h1 class="text-4xl font-bold tracking-tight sm:text-5xl md:text-6xl">
                        {{ $entry->elements->firstWhere('handle', 'hero_title')?->value ?? 'Welcome' }}
                    </h1>
                    @if($subtitle = $entry->elements->firstWhere('handle', 'hero_subtitle')?->value)
                        <p class="mt-6 text-xl max-w-3xl mx-auto">
                            {{ $subtitle }}
                        </p>
                    @endif
                    @if($ctaText = $entry->elements->firstWhere('handle', 'cta_text')?->value)
                        <div class="mt-10">
                            <a href="{{ $entry->elements->firstWhere('handle', 'cta_url')?->value ?? '#' }}" 
                               class="inline-block bg-white text-indigo-600 px-8 py-3 rounded-lg font-semibold hover:bg-zinc-100 transition">
                                {{ $ctaText }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Content Section --}}
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="prose prose-lg dark:prose-invert max-w-none">
                {!! nl2br(e($entry->elements->firstWhere('handle', 'content')?->value ?? '')) !!}
            </div>
        </div>
    @else
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 text-center">
            <h1 class="text-4xl font-bold text-zinc-900 dark:text-white">Welcome</h1>
            <p class="mt-4 text-zinc-600 dark:text-zinc-400">Content coming soon...</p>
        </div>
    @endif
</div>
