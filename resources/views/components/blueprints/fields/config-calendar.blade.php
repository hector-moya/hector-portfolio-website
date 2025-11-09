<div class="space-y-6">
    <flux:heading size="lg">{{ __('Calendar Field Settings') }}</flux:heading>
    <flux:radio.group label="{{ __('Choose Calendar Mode') }}" wire:model.live="form.config.mode">
        <flux:radio value="multiple" label="{{ __('Multiple Dates Selection') }}" checked />
        <flux:radio value="range" label="{{ __('Date Range Selection') }}" />
    </flux:radio.group>
    @if ($config['mode'] === 'range')
        <div class="grid grid-cols-2 gap-4">
            <flux:input label="{{ __('Minimum Range (days)') }}" type="number" placeholder="{{ __('Eg. 3') }}" wire:model="form.config.min_range" />
            <flux:input label="{{ __('Maximum Range (days)') }}" type="number" placeholder="{{ __('Eg. 7') }}" wire:model="form.config.max_range" />
        </div>
    @endif
</div>
