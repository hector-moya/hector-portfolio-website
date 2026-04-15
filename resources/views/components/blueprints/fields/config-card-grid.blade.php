<div class="space-y-6">
    <flux:heading size="lg">{{ __('Card Grid Field Settings') }}</flux:heading>

    <flux:select label="{{ __('Columns') }}" wire:model="form.selectedFieldConfig.columns">
        <flux:select.option value="2">{{ __('2 Columns') }}</flux:select.option>
        <flux:select.option value="3">{{ __('3 Columns') }}</flux:select.option>
        <flux:select.option value="4">{{ __('4 Columns') }}</flux:select.option>
    </flux:select>

    <flux:input
        type="number"
        label="{{ __('Max Cards') }}"
        wire:model.blur="form.selectedFieldConfig.max_items"
        placeholder="{{ __('No limit') }}"
        min="1"
        description="{{ __('Leave blank to allow unlimited cards.') }}"
    />
</div>
