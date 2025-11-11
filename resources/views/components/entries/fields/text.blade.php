<flux:input
    type="text"
    label="{{ $field->label }}"
    placeholder="{{ $field->config['placeholder'] ?? '' }}"
    maxlength="{{ $field->config['max'] ?? '' }}"
    wire:model="form.fieldValues.{{ $field->handle }}"
/>
<flux:error name="form.fieldValues.{{ $field->handle }}" />
@if ($field->instructions)
    <flux:description>{{ $field->instructions }}</flux:description>
@endif
