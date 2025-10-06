<div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-zinc-900 dark:text-white">Blog</h1>
            <p class="mt-4 text-xl text-zinc-600 dark:text-zinc-400">
                Latest articles and insights
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($posts as $post)
                <article class="bg-white dark:bg-zinc-800 rounded-lg shadow-md overflow-hidden hover:shadow-xl transition">
                    @if($featuredImage = $post->elements->firstWhere('handle', 'featured_image')?->value)
                        <img src="{{ $featuredImage }}" alt="{{ $post->title }}" class="w-full h-48 object-cover">
                    @endif
                    <div class="p-6">
                        @if($category = $post->elements->firstWhere('handle', 'category')?->value)
                            <span class="text-sm font-semibold text-indigo-600 dark:text-indigo-400">{{ $category }}</span>
                        @endif
                        <h2 class="mt-2 text-xl font-bold text-zinc-900 dark:text-white">
                            <a href="{{ route('blog.show', $post->slug) }}" wire:navigate class="hover:text-indigo-600 dark:hover:text-indigo-400">
                                {{ $post->title }}
                            </a>
                        </h2>
                        @if($excerpt = $post->elements->firstWhere('handle', 'excerpt')?->value)
                            <p class="mt-3 text-zinc-600 dark:text-zinc-400 line-clamp-3">
                                {{ $excerpt }}
                            </p>
                        @endif
                        <div class="mt-4 flex items-center text-sm text-zinc-500 dark:text-zinc-400">
                            <span>{{ $post->author->name }}</span>
                            <span class="mx-2">•</span>
                            <span>{{ $post->published_at->format('M d, Y') }}</span>
                            @if($readingTime = $post->elements->firstWhere('handle', 'reading_time')?->value)
                                <span class="mx-2">•</span>
                                <span>{{ $readingTime }} min read</span>
                            @endif
                        </div>
                    </div>
                </article>
            @empty
                <div class="col-span-3 text-center py-12">
                    <p class="text-zinc-600 dark:text-zinc-400">No blog posts available yet.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-12">
            {{ $posts->links() }}
        </div>
    </div>
</div>