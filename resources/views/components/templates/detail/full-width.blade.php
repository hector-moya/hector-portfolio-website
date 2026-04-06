@props(['entry', 'theme' => 'greenpeace'])
@php
    $fields = $entry->blueprint->tabs->sortBy('sort_order')
        ->flatMap(fn($tab) => $tab->sections->sortBy('sort_order')
            ->flatMap(fn($section) => $section->fields->sortBy('order')))
        ->reject(fn($field) => $field->type === 'page_builder');

    $heroImage = $fields->first(fn($f) => $f->type === 'image');
    $heroImageValue = $heroImage
        ? $entry->elements->firstWhere('handle', $heroImage->handle)?->getElementValue()
        : null;
@endphp

{{-- Full-bleed hero --}}
@if($heroImageValue)
    <div class="relative w-full h-96 overflow-hidden">
        <img src="{{ $heroImageValue }}" alt="{{ $entry->title }}" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-linear-to-t from-zinc-900/80 to-transparent flex items-end">
            <div class="px-8 pb-8 max-w-7xl mx-auto w-full">
                <flux:heading size="xl" class="text-white!">{{ $entry->title }}</flux:heading>
            </div>
        </div>
    </div>
@endif

<x-themes.wrapper :theme="$theme">
    @if(!$heroImageValue)
        <flux:heading size="xl" class="text-white! mb-6">{{ $entry->title }}</flux:heading>
    @endif

    <flux:text class="mb-8 text-zinc-400!">
        @if($entry->author) {{ $entry->author->name }} @endif
        @if($entry->published_at) • {{ $entry->published_at->format('F j, Y') }} @endif
    </flux:text>

    <div class="space-y-6 max-w-7xl">
        @if($fields->isNotEmpty())
            @foreach($fields as $field)
                @if($field->type === 'image') @continue @endif
                @php $value = $entry->elements->firstWhere('handle', $field->handle)?->getElementValue(); @endphp
                @if($value !== null && $value !== '')
                    <x-dynamic-component
                        :component="'field-renderers.' . $field->type"
                        :field="$field"
                        :value="$value"
                    />
                @endif
            @endforeach
        @else
            @foreach($entry->elements as $element)
                @if(!$element->Field || in_array($element->Field->type, ['image', 'page_builder'])) @continue @endif
                @php $value = $element->getElementValue(); @endphp
                @if($value !== null && $value !== '')
                    <x-dynamic-component
                        :component="'field-renderers.' . $element->Field->type"
                        :field="$element->Field"
                        :value="$value"
                    />
                @endif
            @endforeach
        @endif
    </div>

    @foreach($entry->getPageBuilderSections() as $section)
        <x-dynamic-component
            :component="'sections.' . str_replace('_', '-', $section['type'])"
            :section="$section"
            :assets="collect()"
            wire:key="section-{{ $section['_id'] }}"
        />
    @endforeach

    <div class="mt-12">
        <flux:link href="javascript:history.back()" wire:navigate.back>← Back</flux:link>
    </div>
</x-themes.wrapper>
