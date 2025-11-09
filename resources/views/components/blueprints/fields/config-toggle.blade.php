<div class="space-y-6">
    <flux:heading size="lg">{{ __('Toggle Field Settings') }}</flux:heading>
    <div class="grid grid-cols-2 gap-4">
        <flux:input label="{{ __('On Label') }}" placeholder="{{ __('Eg. Yes') }}" wire:model="form.config.on_label" />
        <flux:input label="{{ __('Off Label') }}" placeholder="{{ __('Eg. No') }}" wire:model="form.config.off_label" />
    </div>
</div>
