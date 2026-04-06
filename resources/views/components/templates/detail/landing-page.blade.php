@props(['entry' => null, 'sections' => [], 'assets' => collect(), 'theme' => 'greenpeace'])

<x-themes.wrapper :theme="$theme">
    @forelse($sections as $section)
        <x-dynamic-component
            :component="'sections.' . str_replace('_', '-', $section['type'])"
            :section="$section"
            :assets="$assets"
            wire:key="section-{{ $section['_id'] }}"
        />
    @empty
        <div class="mx-auto max-w-3xl py-24 text-center">
            @if($entry)
                <flux:heading size="xl">{{ $entry->title }}</flux:heading>
            @endif
            <flux:text class="mt-4">{{ __('Content coming soon.') }}</flux:text>
        </div>
    @endforelse
</x-themes.wrapper>
