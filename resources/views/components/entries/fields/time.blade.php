@php
    $includeInput = $field->config['include_input'] ?? true;
    $acceptMultiple = $field->config['accept_multiple'] ?? false;
    $timeFormat = $field->config['time_format'] ?? '24-hour';
    $interval = $field->config['interval'] ?? 30;
    $minTime = $field->config['min_time'] ?? '09:00';
    $maxTime = $field->config['max_time'] ?? '17:00';
    $is12Hour = $timeFormat === '12-hour';
@endphp

<div class="space-y-2">
    <flux:select
        label="{{ $field->label }}"
        wire:model="form.fieldValues.{{ $field->handle }}"
        @if($acceptMultiple) multiple @endif
    >
        <option value="">{{ __('Select time...') }}</option>
        @php
            $current = strtotime($minTime);
            $end = strtotime($maxTime);
            $intervalSeconds = $interval * 60;
        @endphp
        @while($current <= $end)
            @php
                $time24 = date('H:i', $current);
                $timeDisplay = $is12Hour ? date('g:i A', $current) : $time24;
            @endphp
            <option value="{{ $time24 }}">{{ $timeDisplay }}</option>
            @php $current += $intervalSeconds; @endphp
        @endwhile
    </flux:select>

    @if($includeInput)
        <flux:input
            type="time"
            wire:model="form.fieldValues.{{ $field->handle }}"
        />
    @endif

    <flux:error name="form.fieldValues.{{ $field->handle }}" />
    @if ($field->instructions)
        <flux:description>{{ $field->instructions }}</flux:description>
    @endif
</div>
