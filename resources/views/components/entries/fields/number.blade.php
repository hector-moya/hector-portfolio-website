<flux:input
    type="number"
    label="{{ $field->label }}"
    min="{{ $field->config['min'] ?? '' }}"
    max="{{ $field->config['max'] ?? '' }}"
    step="{{ $field->config['step'] ?? 1 }}"
    wire:model="form.fieldValues.{{ $field->handle }}"
/>
<flux:error name="form.fieldValues.{{ $field->handle }}" />
@if ($field->instructions)
    <flux:description>{{ $field->instructions }}</flux:description>
@endif
