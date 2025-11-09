<div class="space-y-6">
    <flux:fieldset>
        <flux:legend>{{ __('Time Field Settings') }}</flux:legend>
        <div class="space-y-4">
            <flux:switch label="{{ __('Include Input') }}" wire:model.live="form.config.include_input" />
            <flux:switch label="{{ __('Accept Multiple') }}" wire:model.live="form.config.accept_multiple" />
        </div>
    </flux:fieldset>
    <div class="grid grid-cols-2 gap-4">
        <flux:select label="{{ __('Select Time Format') }}" wire:model="form.config.time_format">
            <flux:select.option value="24-hour">{{ __('24-hour (HH:MM)') }}</flux:select.option>
            <flux:select.option value="12-hour">{{ __('12-hour (HH:MM AM/PM)') }}</flux:select.option>
        </flux:select>
        <flux:select label="{{ __('Select Interval') }}" wire:model="form.config.interval">
            <flux:select.option value="30">{{ __('30 minutes') }}</flux:select.option>
            <flux:select.option value="60">{{ __('1 hour') }}</flux:select.option>
        </flux:select>
            <flux:input label="{{ __('Minimum Time') }}" placeholder="{{ __('09:00') }}" wire:model="form.config.min_time" />
            <flux:input label="{{ __('Maximum Time') }}" placeholder="{{ __('18:00') }}" wire:model="form.config.max_time" />
        </div>
    </div>
