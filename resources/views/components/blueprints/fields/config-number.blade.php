<div class="space-y-6">
    <flux:heading size="lg">{{ __('Number Field Settings') }}</flux:heading>
    <div class="grid grid-cols-2 gap-4">
        <flux:input label="{{ __('Min Value') }}" placeholder="{{ __('Eg. 0') }}" wire:model="form.config.min" />
        <flux:input label="{{ __('Max Value') }}" placeholder="{{ __('Eg. 100') }}" wire:model="form.config.max" />
        <flux:input label="{{ __('Step Value') }}" placeholder="{{ __('Eg. 1') }}" wire:model="form.config.step" />
    </div>
</div>
