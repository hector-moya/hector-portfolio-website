<div class="grid grid-cols-2 gap-4">
    <flux:input
        label="{{ __('Placeholder') }}"
        wire:model="form.elements.{{ $index }}.config.placeholder"
    />
    <flux:input
        type="number"
        label="{{ __('Max length') }}"
        wire:model="form.elements.{{ $index }}.config.max"
    />
</div>
