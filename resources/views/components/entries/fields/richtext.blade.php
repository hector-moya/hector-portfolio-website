<flux:editor
    label="{{ $field->label }}"
    placeholder="{{ $field->config['placeholder'] ?? '' }}"
    wire:model="form.fieldValues.{{ $field->handle }}"
/>
<flux:error name="form.fieldValues.{{ $field->handle }}" />
@if ($field->instructions)
    <flux:description>{{ $field->instructions }}</flux:description>
@endif
