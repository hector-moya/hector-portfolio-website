<div :key="'field-card-component-'.$field['id']">
    {{-- <flux:card class="!p-0">
        <div class="flex items-center justify-between">
            <flux:icon.grip-vertical variant="micro" class="mx-2 transition-opacity duration-200 hover:opacity-45" />
            <flux:separator vertical />
            <div class="m-2 flex flex-grow items-center">
                <flux:modal.trigger name="field-config-{{ $field['id'] }}">
                    <flux:button size="xs" variant="ghost" icon="{{ $field['icon'] }}" tooltip="{{ __('Configure ') . $field['name'] }}">{{ $field['name'] }}</flux:button>
                </flux:modal.trigger>
            </div>
            <div class="mx-2 flex">
                <flux:button icon="clipboard-document-list" size="xs" variant="ghost" tooltip="{{ __('Duplicate Field') }}" wire:click="$parent.save" />
                <flux:button icon="trash" size="xs" variant="ghost" tooltip="{{ __('Remove Field') }}" wire:click="removeField('{{ $field['id'] }}')" />
            </div>
        </div>
    </flux:card> --}}

    {{-- <flux:modal name="field-config-{{ $field['id'] }}">
        <div class="flex items-start gap-4">
            <div class="flex-1 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <flux:select label="{{ __('Type') }}" wire:model="field.type">
                        @foreach ($this->fieldTypeOptions as $value => $label)
                            <flux:select.option value="{{ $value }}" :key="'field-type-option-{$value}'">{{ $label }}</flux:select.option>
                        @endforeach
                    </flux:select>

                    <flux:input label="{{ __('Label') }}" wire:model.live.debounce.750ms="field.label" placeholder="Eg. Post title" />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <flux:input label="{{ __('Handle') }}" wire:model="field.handle" placeholder="post_title" />

                    <flux:input label="{{ __('Instructions') }}" wire:model="field.instructions" placeholder="Enter the post title" />
                </div>

                <div class="flex justify-end">
                    <flux:switch label="{{ $field['is_required'] ? 'Required' : 'Optional' }}" wire:model.live="field.is_required" />
                </div>

                <div class="mt-4 border-t pt-4">
                    <flux:heading size="sm">{{ __('Field Configuration') }}</flux:heading>

                    @php($type = $field['type'] ?? 'text')

                    @includeIf('blueprints.fields.config-' . $type, ['index' => $field['id'], 'element' => $field])
                </div>
            </div>

            <flux:button type="button" icon="trash" wire:click="removeElement('{{ $field['id'] }}')" size="sm" variant="danger" />
        </div>
    </flux:modal> --}}
</div>
