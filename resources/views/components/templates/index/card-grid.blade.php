@props(['collection', 'entries', 'theme' => 'greenpeace'])
<x-themes.wrapper :theme="$theme">
    <div class="text-center mb-12">
        <flux:heading size="xl" class="text-white!">{{ $collection->name }}</flux:heading>
        @if($collection->description)
            <flux:text class="mt-4 text-zinc-300!">{{ $collection->description }}</flux:text>
        @endif
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($entries as $entry)
            @php
                $rawImage = $entry->elements->firstWhere('handle', 'featured_image')?->getElementValue();
                $featuredImage = is_string($rawImage) ? $rawImage : null;
                $rawExcerpt = $entry->elements->first(fn($el) => in_array($el->field?->type, ['textarea', 'text', 'text_block']) && is_string($el->getElementValue()))?->getElementValue();
                $excerpt = is_string($rawExcerpt) ? $rawExcerpt : null;
            @endphp
            <flux:card class="p-0! overflow-hidden hover:shadow-xl transition">
                @if($featuredImage)
                    <img src="{{ $featuredImage }}" alt="{{ $entry->title }}" class="w-full h-48 object-cover">
                @endif
                <div class="p-6 space-y-3">
                    <flux:heading size="lg">
                        <a href="{{ route('entry.show', [$collection->slug, $entry->slug]) }}" wire:navigate class="hover:text-indigo-600 dark:hover:text-indigo-400">
                            {{ $entry->title }}
                        </a>
                    </flux:heading>
                    @if($excerpt)
                        <flux:text class="line-clamp-3">{{ $excerpt }}</flux:text>
                    @endif
                    <div class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400">
                        @if($entry->author)
                            <span>{{ $entry->author->name }}</span>
                            <span>•</span>
                        @endif
                        @if($entry->published_at)
                            <span>{{ $entry->published_at->format('M d, Y') }}</span>
                        @endif
                    </div>
                </div>
            </flux:card>
        @empty
            <div class="col-span-3 text-center py-12">
                <flux:text>No entries available yet.</flux:text>
            </div>
        @endforelse
    </div>

    @if($entries->hasPages())
        <div class="mt-12">{{ $entries->links() }}</div>
    @endif
</x-themes.wrapper>
