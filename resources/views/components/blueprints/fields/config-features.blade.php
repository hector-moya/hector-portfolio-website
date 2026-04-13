<div class="space-y-6">
    <flux:heading size="lg">{{ __('Features Field Settings') }}</flux:heading>
    <div class="grid grid-cols-2 gap-4">
        <flux:input type="number" label="{{ __('Max Items') }}" placeholder="{{ __('Leave blank for unlimited') }}" wire:model="form.config.max_items" />
    </div>
</div>
