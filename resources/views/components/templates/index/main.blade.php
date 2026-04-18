@props(['collection', 'sections' => [], 'assets', 'childEntries', 'childTemplate' => 'card-grid', 'theme' => 'greenpeace'])

<x-themes.wrapper :theme="$theme">
    {{-- CMS-editable page builder sections for this collection's landing entry --}}
    @foreach($sections as $section)
        <x-dynamic-component
            :component="'fields.' . str_replace('_', '-', $section['type'])"
            :section="$section"
            :assets="$assets"
            wire:key="section-{{ $section['_id'] }}"
        />
    @endforeach

    {{-- Child entries listing --}}
    @if($childEntries->isNotEmpty())
        <div class="mt-16">
            @if(count($sections) > 0)
                <flux:separator class="mb-12" />
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($childEntries as $entry)
                    @php
                        $featuredImage = $entry->elements->firstWhere('handle', 'featured_image')?->getElementValue();
                        $excerpt = $entry->elements->first(fn($el) => in_array($el->field?->type, ['textarea', 'text']) && $el->getElementValue())?->getElementValue();
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
                @endforeach
            </div>
        </div>
    @elseif(count($sections) === 0)
        <div class="mx-auto max-w-3xl py-24 text-center">
            <flux:heading size="xl" class="text-white!">{{ $collection->name }}</flux:heading>
            <flux:text class="mt-4">{{ __('Content coming soon.') }}</flux:text>
        </div>
    @endif
</x-themes.wrapper>
