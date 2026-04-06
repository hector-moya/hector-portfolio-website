@props(['entry', 'theme' => 'greenpeace'])
@php
    $fields = $entry->blueprint->tabs->sortBy('sort_order')
        ->flatMap(fn($tab) => $tab->sections->sortBy('sort_order')
            ->flatMap(fn($section) => $section->fields->sortBy('order')))
        ->reject(fn($field) => $field->type === 'page_builder');
@endphp

<x-themes.wrapper :theme="$theme">
    <div class="mx-auto max-w-2xl">
        <flux:heading size="xl" class="text-white! mb-2">{{ $entry->title }}</flux:heading>
        <flux:text class="mb-8 text-zinc-400!">
            @if($entry->author) {{ $entry->author->name }} @endif
            @if($entry->published_at) • {{ $entry->published_at->format('F j, Y') }} @endif
        </flux:text>

        <flux:separator class="mb-8" />

        <dl class="space-y-6">
            @foreach($fields as $field)
                @php $value = $entry->elements->firstWhere('handle', $field->handle)?->getElementValue(); @endphp
                @if($value !== null && $value !== '')
                    <div>
                        <flux:text class="text-sm font-semibold text-zinc-500 dark:text-zinc-400 mb-1">{{ $field->label }}</flux:text>
                        <x-dynamic-component
                            :component="'field-renderers.' . $field->type"
                            :field="$field"
                            :value="$value"
                        />
                    </div>
                @endif
            @endforeach
        </dl>
    </div>
</x-themes.wrapper>
