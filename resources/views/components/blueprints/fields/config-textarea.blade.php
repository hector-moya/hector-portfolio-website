<div class="space-y-6">
    <flux:heading size="lg">{{ __('Textarea Field Settings') }}</flux:heading>
    <div class="grid grid-cols-2 gap-4">
        <flux:input label="{{ __('Placeholder') }}" placeholder="{{ __('Eg. Enter text here') }}" wire:model="form.config.placeholder" />
        <flux:input label="{{ __('Rows') }}" type="number" placeholder="{{ __('Eg. 4. 0 for auto.') }}" wire:model="form.config.rows" />
    </div>
</div>
