<flux:input
  label="{{ $field->label }}"
  placeholder="{{ $field->config['placeholder'] ?? '' }}"
  wire:model="form.fieldValues.{{ $field->handle }}"
/>
<flux:error name="form.fieldValues.{{ $field->handle }}" />
