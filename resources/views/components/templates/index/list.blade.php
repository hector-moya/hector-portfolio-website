@props(['collection', 'entries', 'theme' => 'greenpeace'])
<x-themes.wrapper :theme="$theme">
    <div class="mb-10">
        <flux:heading size="xl" class="text-white!">{{ $collection->name }}</flux:heading>
        @if($collection->description)
            <flux:text class="mt-2 text-zinc-300!">{{ $collection->description }}</flux:text>
        @endif
    </div>

    <div class="space-y-4">
        @forelse($entries as $entry)
            @php
                $featuredImage = $entry->elements->firstWhere('handle', 'featured_image')?->getElementValue();
                $excerpt = $entry->elements->first(fn($el) => in_array($el->Field?->type, ['textarea', 'text']) && $el->getElementValue())?->getElementValue();
            @endphp
            <flux:card>
                <div class="flex gap-4 items-start">
                    @if($featuredImage)
                        <img src="{{ $featuredImage }}" alt="{{ $entry->title }}" class="w-20 h-20 object-cover rounded-lg shrink-0">
                    @endif
                    <div class="flex-1 min-w-0">
                        <flux:heading size="md">
                            <a href="{{ route('entry.show', [$collection->slug, $entry->slug]) }}" wire:navigate class="hover:text-indigo-600 dark:hover:text-indigo-400">
                                {{ $entry->title }}
                            </a>
                        </flux:heading>
                        @if($excerpt)
                            <flux:text class="mt-1 line-clamp-2">{{ $excerpt }}</flux:text>
                        @endif
                        <flux:text class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">
                            @if($entry->author) {{ $entry->author->name }} • @endif
                            @if($entry->published_at) {{ $entry->published_at->format('M d, Y') }} @endif
                        </flux:text>
                    </div>
                </div>
            </flux:card>
        @empty
            <flux:text>No entries available yet.</flux:text>
        @endforelse
    </div>

    <div class="mt-8">{{ $entries->links() }}</div>
</x-themes.wrapper>
