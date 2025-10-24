<flux:select label="{{ $element->label }}" wire:model="form.fieldValues.{{ $element->handle }}">
  <option value="">{{ __('Select an option...') }}</option>
  @foreach(($element->config['options'] ?? []) as $opt)
    <option value="{{ $opt['value'] ?? $opt }}">{{ $opt['label'] ?? $opt }}</option>
  @endforeach
</flux:select>
<flux:error name="form.fieldValues.{{ $element->handle }}" />
@if ($element->instructions)
  <flux:description>{{ $element->instructions }}</flux:description>
@endif
