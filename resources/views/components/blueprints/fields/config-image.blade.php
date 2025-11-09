<div class="space-y-6">
    <flux:heading size="lg">{{ __('Image Field Settings') }}</flux:heading>
    <div class="grid grid-cols-2 gap-4">
        <flux:input label="{{ __('Max Size (MB)') }}" type="number" placeholder="{{ __('Eg. 5') }}" wire:model="form.config.max_size_mb" />
        <flux:input label="{{ __('Allowed Mimes (comma separated)') }}" placeholder="{{ __('Eg. jpg, png, webp') }}" wire:model="form.config.mimes" />
    </div>
</div>
