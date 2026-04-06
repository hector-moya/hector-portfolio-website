@props(['entry', 'theme' => 'greenpeace'])
@php
    $fields = $entry->blueprint->tabs->sortBy('sort_order')
        ->flatMap(fn($tab) => $tab->sections->sortBy('sort_order')
            ->flatMap(fn($section) => $section->fields->sortBy('order')))
        ->reject(fn($field) => $field->type === 'page_builder');

    $firstImage = $fields->first(fn($f) => $f->type === 'image');
    $firstImageValue = $firstImage
        ? $entry->elements->firstWhere('handle', $firstImage->handle)?->getElementValue()
        : null;
@endphp

<x-themes.wrapper :theme="$theme">
    <div class="mx-auto max-w-3xl">
        {{-- Meta --}}
        <div class="mb-8">
            <flux:heading size="xl" class="text-white!">{{ $entry->title }}</flux:heading>
            <flux:text class="mt-3 text-zinc-400!">
                @if($entry->author) {{ $entry->author->name }} @endif
                @if($entry->published_at) • {{ $entry->published_at->format('F j, Y') }} @endif
            </flux:text>
        </div>

        {{-- Featured image --}}
        @if($firstImageValue)
            <img src="{{ $firstImageValue }}" alt="{{ $entry->title }}" class="w-full h-80 object-cover rounded-xl mb-8">
        @endif

        {{-- Fields --}}
        <div class="space-y-6">
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
                    @if($element->Field && $element->Field->type !== 'image' && $element->Field->type !== 'page_builder')
                        @php $value = $element->getElementValue(); @endphp
                        @if($value !== null && $value !== '')
                            <x-dynamic-component
                                :component="'field-renderers.' . $element->Field->type"
                                :field="$element->Field"
                                :value="$value"
                            />
                        @endif
                    @endif
                @endforeach
            @endif
        </div>

        {{-- Page builder sections --}}
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
    </div>
</x-themes.wrapper>
