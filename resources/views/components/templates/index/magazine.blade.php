@props(['collection', 'entries', 'theme' => 'greenpeace'])
<x-themes.wrapper :theme="$theme">
    <div class="mb-10">
        <flux:heading size="xl" class="text-white!">{{ $collection->name }}</flux:heading>
        @if($collection->description)
            <flux:text class="mt-2 text-zinc-300!">{{ $collection->description }}</flux:text>
        @endif
    </div>

    @php $featured = $entries->first(); $rest = $entries->slice(1); @endphp

    @if($featured)
        @php
            $rawImage = $featured->elements->firstWhere('handle', 'featured_image')?->getElementValue();
            $featuredImage = is_string($rawImage) ? $rawImage : null;
            $rawExcerpt = $featured->elements->first(fn($el) => in_array($el->field?->type, ['textarea', 'text', 'text_block']) && is_string($el->getElementValue()))?->getElementValue();
            $excerpt = is_string($rawExcerpt) ? $rawExcerpt : null;
        @endphp
        <flux:card class="p-0! overflow-hidden mb-8">
            @if($featuredImage)
                <img src="{{ $featuredImage }}" alt="{{ $featured->title }}" class="w-full h-72 object-cover">
            @endif
            <div class="p-8 space-y-3">
                <flux:heading size="xl">
                    <a href="{{ route('entry.show', [$collection->slug, $featured->slug]) }}" wire:navigate class="hover:text-indigo-600 dark:hover:text-indigo-400">
                        {{ $featured->title }}
                    </a>
                </flux:heading>
                @if($excerpt)
                    <flux:text class="text-lg line-clamp-3">{{ $excerpt }}</flux:text>
                @endif
                <flux:text class="text-sm text-zinc-500 dark:text-zinc-400">
                    @if($featured->author) {{ $featured->author->name }} • @endif
                    @if($featured->published_at) {{ $featured->published_at->format('M d, Y') }} @endif
                </flux:text>
            </div>
        </flux:card>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach($rest as $entry)
            @php
                $rawExcerpt = $entry->elements->first(fn($el) => in_array($el->field?->type, ['textarea', 'text', 'text_block']) && is_string($el->getElementValue()))?->getElementValue();
                $excerpt = is_string($rawExcerpt) ? $rawExcerpt : null;
            @endphp
            <flux:card>
                <flux:heading size="md">
                    <a href="{{ route('entry.show', [$collection->slug, $entry->slug]) }}" wire:navigate class="hover:text-indigo-600 dark:hover:text-indigo-400">
                        {{ $entry->title }}
                    </a>
                </flux:heading>
                @if($excerpt)
                    <flux:text class="mt-2 line-clamp-2">{{ $excerpt }}</flux:text>
                @endif
                <flux:text class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">
                    @if($entry->published_at) {{ $entry->published_at->format('M d, Y') }} @endif
                </flux:text>
            </flux:card>
        @endforeach
    </div>

    @if($entries->hasPages())
        <div class="mt-8">{{ $entries->links() }}</div>
    @endif
</x-themes.wrapper>
