@props(['config' => [], 'children' => []])

<div class="space-y-4">
    <flux:heading size="lg">{{ __('Page Builder Settings') }}</flux:heading>
    <flux:checkbox.group
        label="{{ __('Allowed Section Types') }}"
        description="{{ __('Choose which section types editors can add to this field.') }}"
        wire:model="form.config.allowed_section_types"
    >
        <div class="flex flex-wrap gap-2">
            <flux:checkbox.all label="{{ __('All') }}" />
            @foreach (\App\Support\SectionTypes::labels() as $value => $label)
                <flux:checkbox value="{{ $value }}" label="{{ $label }}" />
            @endforeach
        </div>
    </flux:checkbox.group>
</div>
