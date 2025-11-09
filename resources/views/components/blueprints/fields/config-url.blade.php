<div class="space-y-6">
    <flux:heading size="lg">{{ __('URL Field Settings') }}</flux:heading>
    <div class="grid grid-cols-2 gap-4">
        <flux:input label="{{ __('Placeholder Text') }}" placeholder="{{ __('Eg. https://example.com') }}" wire:model="form.config.placeholder" />
    </div>
</div>
