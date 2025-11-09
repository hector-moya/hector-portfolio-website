@props([
    'config' => [],
])

<div class="space-y-6">
    <flux:fieldset>
        <flux:legend>{{ __('Date Field Settings') }}</flux:legend>
        <div class="space-y-4">
            <flux:switch label="{{ __('Include date range') }}" wire:model.live="form.config.date_range" />
            @if ($config['date_range'])
                <flux:switch label="{{ __('Include separate range inputs') }}" wire:model.live="form.config.include_separate_range_inputs" />
            @endif
            <flux:switch label="{{ __('Include presets') }}" wire:model.live="form.config.include_presets" />
        </div>
    </flux:fieldset>

    <div class="grid grid-cols-2 gap-4">
        @if ($config['date_range'])
            <flux:input label="{{ __('Start date') }}" type="date" placeholder="{{ __('Select a start date') }}" wire:model="form.config.start_date" />
            <flux:input label="{{ __('End date') }}" type="date" placeholder="{{ __('Select an end date') }}" wire:model="form.config.end_date" />
            <flux:input label="{{ __('Minimun range') }}" type="number" placeholder="{{ __('Eg. 3') }}" wire:model="form.config.min_range" />
            <flux:input label="{{ __('Maximum range') }}" type="number" placeholder="{{ __('Eg. 7') }}" wire:model="form.config.max_range" />
        @endif
        @if ($config['include_presets'])
            <flux:select variant="listbox" multiple label="{{ __('Select presets') }}" placeholder="{{ __('Select one or more presets') }}" wire:model="form.config.presets">
                <flux:select.option value="today">{{ __('Today') }}</flux:select.option>
                <flux:select.option value="yesterday">{{ __('Yesterday') }}</flux:select.option>
                <flux:select.option value="this_week">{{ __('This Week') }}</flux:select.option>
                <flux:select.option value="last_7_days">{{ __('Last 7 Days') }}</flux:select.option>
                <flux:select.option value="this_month">{{ __('This Month') }}</flux:select.option>
                <flux:select.option value="year_to_date">{{ __('Year to Date') }}</flux:select.option>
                <flux:select.option value="all_time">{{ __('All Time') }}</flux:select.option>
            </flux:select>
        @endif
    </div>
</div>
