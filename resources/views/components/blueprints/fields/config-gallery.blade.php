<div class="space-y-6">
    <flux:heading size="lg">{{ __('Gallery Field Settings') }}</flux:heading>
    <div class="grid grid-cols-2 gap-4">
        <flux:input type="number" label="{{ __('Max Images') }}" placeholder="{{ __('Eg. 6') }}" wire:model="form.config.max_images" />
    </div>
</div>
