<flux:input
    type="email"
    label="{{ $element->label }}"
    placeholder="{{ $element->config['placeholder'] ?? '' }}"
    wire:model="form.fieldValues.{{ $element->handle }}"
/>
<flux:error name="form.fieldValues.{{ $element->handle }}" />
@if ($element->instructions)
    <flux:description>{{ $element->instructions }}</flux:description>
@endif
