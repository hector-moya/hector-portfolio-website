@props(['entry' => null, 'sections' => [], 'assets', 'theme' => 'greenpeace'])

@php
    $featuredImage = $entry?->elements->firstWhere('handle', 'featured_image')?->getElementValue();
    $excerpt = $entry?->elements->firstWhere('handle', 'excerpt')?->getElementValue();
    $content = $entry?->elements->firstWhere('handle', 'content')?->getElementValue();
@endphp

<x-themes.wrapper :theme="$theme">
    <article class="mx-auto max-w-3xl">
        {{-- Featured Image --}}
        @if($featuredImage)
            <div class="mb-8 overflow-hidden rounded-xl">
                <img src="{{ $featuredImage }}" alt="{{ $entry->title }}" class="w-full max-h-96 object-cover">
            </div>
        @endif

        {{-- Title & Meta --}}
        <header class="mb-8">
            <flux:heading size="xl" class="text-white! mb-4">{{ $entry?->title }}</flux:heading>

            <div class="flex items-center gap-3 text-sm text-zinc-400">
                @if($entry?->author)
                    <span>{{ $entry->author->name }}</span>
                    <span>•</span>
                @endif
                @if($entry?->published_at)
                    <span>{{ $entry->published_at->format('M d, Y') }}</span>
                @endif
            </div>

            @if($excerpt)
                <flux:text class="mt-4 text-lg text-zinc-300! leading-relaxed">{{ $excerpt }}</flux:text>
            @endif
        </header>

        {{-- Content: page builder sections if present, otherwise raw value --}}
        @if($sections)
            <div class="space-y-8">
                @foreach($sections as $section)
                    <x-dynamic-component
                        :component="'fields.' . str_replace('_', '-', $section['type'])"
                        :section="$section"
                        :assets="$assets"
                    />
                @endforeach
            </div>
        @elseif($content)
            <div class="prose prose-invert max-w-none">
                {!! $content !!}
            </div>
        @endif
    </article>
</x-themes.wrapper>
