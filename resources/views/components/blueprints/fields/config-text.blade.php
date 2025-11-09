<div class="space-y-6">
    <flux:heading size="lg">{{ __('Text Field Settings') }}</flux:heading>
    <div class="grid grid-cols-2 gap-4">
        <flux:input label="{{ __('Placeholder') }}" placeholder="{{ __('Eg. Enter text here') }}" wire:model="form.config.placeholder" />
        <flux:input label="{{ __('Max Length') }}" type="number" placeholder="{{ __('Eg. 50') }}" wire:model="form.config.max" />
    </div>
</div>
