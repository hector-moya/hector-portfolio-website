<div>
    <x-themes.greenpeace>
        {{-- Featured Image --}}
        @if($featuredImage = $entry->elements->firstWhere('handle', 'featured_image')?->value)
            <img src="{{ $featuredImage }}" alt="{{ $entry->title }}" class="w-full h-96 object-cover rounded-lg mb-8">
        @endif

        {{-- Meta Info --}}
        <div class="mb-8">
            @if($category = $entry->elements->firstWhere('handle', 'category')?->value)
                <span class="text-sm font-semibold text-indigo-600 dark:text-indigo-400">{{ $category }}</span>
            @endif
            <h1 class="mt-2 text-4xl font-bold text-zinc-900 dark:text-white">
                {{ $entry->title }}
            </h1>
            <div class="mt-4 flex items-center text-sm text-zinc-500 dark:text-zinc-400">
                <span>By {{ $entry->author->name }}</span>
                <span class="mx-2">•</span>
                <span>{{ $entry->published_at->format('F d, Y') }}</span>
                @if($readingTime = $entry->elements->firstWhere('handle', 'reading_time')?->value)
                    <span class="mx-2">•</span>
                    <span>{{ $readingTime }} min read</span>
                @endif
            </div>
        </div>

        {{-- Content --}}
        <div class="prose prose-lg dark:prose-invert max-w-none">
            {!! nl2br(e($entry->elements->firstWhere('handle', 'content')?->value ?? '')) !!}
        </div>

        {{-- Tags --}}
        @if($tags = $entry->elements->firstWhere('handle', 'tags')?->value)
            <div class="mt-12 pt-8 border-t border-zinc-200 dark:border-zinc-700">
                <h3 class="text-sm font-semibold text-zinc-900 dark:text-white mb-3">Tags</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach(explode(',', $tags) as $tag)
                        <span class="px-3 py-1 bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 rounded-full text-sm">
                            {{ trim($tag) }}
                        </span>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Back Link --}}
        <div class="mt-12">
            <a href="{{ route('blog.index') }}" wire:navigate class="text-indigo-600 dark:text-indigo-400 hover:underline">
                ← Back to Blog
            </a>
        </div>
    </x-themes.greenpeace>
</div>
