<div class="space-y-3">
    <flux:input
        type="number"
        label="{{ __('Max characters (optional)') }}"
        wire:model="form.elements.{{ $index }}.config.max"
    />
    <flux:checkbox.group
        label="{{ __('Toolbar items') }}"
        wire:model="form.elements.{{ $index }}.config.toolbar"
    >
        @foreach (['bold','italic','link','h2','h3','ul','ol','blockquote','code'] as $tool)
            <flux:checkbox value="{{ $tool }}">{{ strtoupper($tool) }}</flux:checkbox>
        @endforeach
    </flux:checkbox.group>
</div>
