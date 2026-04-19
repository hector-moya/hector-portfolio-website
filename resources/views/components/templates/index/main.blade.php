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
        <div class="relative overflow-hidden bg-zinc-50 py-16 dark:bg-zinc-900 sm:py-24">
            {{-- Decorative accent --}}
            <div class="pointer-events-none absolute -right-24 -top-24 h-80 w-80 rounded-full bg-teal-100/60 blur-3xl dark:bg-teal-900/20" aria-hidden="true"></div>

            @if(count($sections) > 0)
                <flux:separator class="mb-12" />
            @endif

            <div class="relative mx-auto max-w-7xl px-6 lg:px-8">
                <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($childEntries as $entry)
                        @php
                            $featuredImage = $entry->elements->firstWhere('handle', 'featured_image')?->getElementValue();
                            $excerpt = $entry->elements->first(fn($el) => in_array($el->field?->type, ['textarea', 'text']) && $el->getElementValue())?->getElementValue();
                        @endphp
                        <flux:card class="group overflow-hidden p-0! transition-all duration-300 hover:-translate-y-1 hover:shadow-lg dark:hover:border-teal-700/50 dark:hover:shadow-teal-950/50">
                            @if($featuredImage)
                                <div class="overflow-hidden">
                                    <img src="{{ $featuredImage }}" alt="{{ $entry->title }}" class="aspect-video w-full object-cover transition-transform duration-500 group-hover:scale-105">
                                </div>
                            @endif
                            <div class="space-y-3 p-6">
                                <flux:heading size="lg">
                                    <a href="{{ route('entry.show', [$collection->slug, $entry->slug]) }}" wire:navigate class="hover:text-teal-600 dark:hover:text-teal-400">
                                        {{ $entry->title }}
                                    </a>
                                </flux:heading>
                                @if($excerpt)
                                    <flux:text class="line-clamp-3 text-zinc-500 dark:text-zinc-300">{{ $excerpt }}</flux:text>
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
        </div>
    @elseif(count($sections) === 0)
        <div class="mx-auto max-w-3xl py-24 text-center">
            <flux:heading size="xl" class="text-white!">{{ $collection->name }}</flux:heading>
            <flux:text class="mt-4">{{ __('Content coming soon.') }}</flux:text>
        </div>
    @endif
</x-themes.wrapper>
