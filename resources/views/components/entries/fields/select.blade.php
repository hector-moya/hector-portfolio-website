<flux:select label="{{ $field->label }}" wire:model="form.fieldValues.{{ $field->handle }}">
  <option value="">{{ __('Select an option...') }}</option>
  @foreach(($field->config['options'] ?? []) as $opt)
    <option value="{{ $opt['value'] ?? $opt }}">{{ $opt['label'] ?? $opt }}</option>
  @endforeach
</flux:select>
<flux:error name="form.fieldValues.{{ $field->handle }}" />
@if ($field->instructions)
  <flux:description>{{ $field->instructions }}</flux:description>
@endif
