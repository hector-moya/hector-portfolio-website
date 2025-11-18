@props([
    'index' => null,
    'parentHandle' => null,
    'field' => null,
])

@php
    $wirePath = $parentHandle !== null
        ? "form.fieldValues.{$parentHandle}.items.{$index}.{$field->handle}"
        : "form.fieldValues.{$field->handle}";
@endphp

<flux:select label="{{ $field->label }}" wire:model="{{ $wirePath }}">
  <option value="">{{ __('Select an option...') }}</option>
  @foreach(($field->config['options'] ?? []) as $opt)
    <option value="{{ $opt['value'] ?? $opt }}">{{ $opt['label'] ?? $opt }}</option>
  @endforeach
</flux:select>
<flux:error name="{{ $wirePath }}" />
@if ($field->instructions)
  <flux:description>{{ $field->instructions }}</flux:description>
@endif
