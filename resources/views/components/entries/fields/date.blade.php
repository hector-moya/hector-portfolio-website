@php
    $dateRange = $field->config['date_range'] ?? true;
    $includeSeparateInputs = $field->config['include_separate_range_inputs'] ?? false;
    $includePresets = $field->config['include_presets'] ?? false;
    $presets = $field->config['presets'] ?? [];
    $minRange = $field->config['min_range'] ?? null;
    $maxRange = $field->config['max_range'] ?? null;
@endphp

<div class="space-y-2">
    <flux:date-picker
        label="{{ $field->label }}"
        wire:model="form.fieldValues.{{ $field->handle }}"
        @if($dateRange) range @endif
        @if($includeSeparateInputs) separate-range @endif
        @if($minRange) :min="'{{ $minRange }}'" @endif
        @if($maxRange) :max="'{{ $maxRange }}'" @endif
    >
        @if($includePresets && !empty($presets))
            @foreach($presets as $preset)
                <flux:date-picker.preset label="{{ $preset['label'] }}" value="{{ $preset['value'] }}" />
            @endforeach
        @endif
    </flux:date-picker>

    <flux:error name="form.fieldValues.{{ $field->handle }}" />
    @if ($field->instructions)
        <flux:description>{{ $field->instructions }}</flux:description>
    @endif
</div>
