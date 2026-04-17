<div>
    @if ($entry && !empty($layout))
        @foreach ($layout as $section)
            <x-dynamic-component
                :component="'fields.' . str_replace('_', '-', $section['type'])"
                :section="$section"
                :assets="$assets"
                wire:key="section-{{ $section['_id'] }}"
            />
        @endforeach
    @else
        <x-themes.wrapper :theme="$theme">
            <div class="mx-auto max-w-3xl space-y-8 text-center">
                <flux:heading class="text-4xl!">{{ __('Welcome') }}</flux:heading>
                <flux:text>{{ __('Content coming soon...') }}</flux:text>
            </div>
        </x-themes.wrapper>
    @endif
</div>
