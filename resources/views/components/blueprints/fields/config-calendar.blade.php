<div class="space-y-6">
    <flux:fieldset>
        <flux:legend>{{ __('Calendar Field Settings') }}</flux:legend>
        <div class="space-y-4">
            <flux:switch label="{{ __('Accept Multiple') }}" wire:model.live="form.config.accept_multiple" />
        </div>
    </flux:fieldset>
</div>
