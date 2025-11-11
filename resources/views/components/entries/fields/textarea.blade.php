<flux:textarea
    label="{{ $field->label }}"
    placeholder="{{ $field->config['placeholder'] ?? '' }}"
    rows="{{ $field->config['rows'] ?? 4 }}"
    wire:model="form.fieldValues.{{ $field->handle }}"
/>
<flux:error name="form.fieldValues.{{ $field->handle }}" />
@if ($field->instructions)
    <flux:description>{{ $field->instructions }}</flux:description>
@endif
