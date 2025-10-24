<flux:input
  label="{{ $element->label }}"
  placeholder="{{ $element->config['placeholder'] ?? '' }}"
  wire:model="form.fieldValues.{{ $element->handle }}"
/>
<flux:error name="form.fieldValues.{{ $element->handle }}" />
