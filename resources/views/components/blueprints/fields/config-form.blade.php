<div class="space-y-6">
    <flux:heading size="lg">{{ __('Form Field Settings') }}</flux:heading>
    <flux:checkbox.group
        label="{{ __('Allowed Field Types') }}"
        description="{{ __('Choose which field types editors can add to this form.') }}"
        wire:model="form.config.available_field_types"
    >
        <div class="flex flex-wrap gap-2">
            <flux:checkbox.all label="{{ __('All') }}" />
            @foreach (['text', 'textarea', 'email', 'number', 'date', 'time', 'toggle', 'select', 'radio'] as $fieldType)
                <flux:checkbox value="{{ $fieldType }}" label="{{ ucfirst($fieldType) }}" />
            @endforeach
        </div>
    </flux:checkbox.group>
</div>
