@props(['field', 'value'])
<flux:badge variant="{{ $value ? 'success' : 'zinc' }}">
    {{ $value ? ($field->config['on_label'] ?? 'Yes') : ($field->config['off_label'] ?? 'No') }}
</flux:badge>
