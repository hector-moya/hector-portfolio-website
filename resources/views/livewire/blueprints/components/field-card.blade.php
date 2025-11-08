<div>
    <flux:card class="!p-0">
        <div class="flex items-center justify-between">
            <flux:icon.grip-vertical variant="micro" class="mx-2 transition-opacity duration-200 hover:opacity-45" />
            <flux:separator vertical />
            <div class="m-2 flex flex-grow items-center">
                <flux:modal.trigger name="field-config-{{ $fieldId }}">
                    <flux:button size="xs" variant="ghost" icon="{{ $form->icon }}" tooltip="{{ __('Configure ') . $form->label }}">{{ $form->label }}</flux:button>
                </flux:modal.trigger>
            </div>
            <div class="mx-2 flex">
                <flux:button icon="clipboard-document-list" size="xs" variant="ghost" tooltip="{{ __('Duplicate Field') }}" wire:click="save" />
                <flux:button icon="trash" size="xs" variant="ghost" tooltip="{{ __('Remove Field') }}" wire:click="removeField('{{ $fieldId }}')" />
            </div>
        </div>
    </flux:card>

    <flux:modal name="field-config-{{ $fieldId }}" class="min-w-140">
        <div class="flex items-start gap-4">
            <div class="flex-1 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <flux:select label="{{ __('Type') }}" wire:model="field.type">
                        @foreach ($this->fieldTypeOptions as $value => $label)
                            <flux:select.option value="{{ $value }}" :key="'field-type-option-{$value}'">{{ $label }}</flux:select.option>
                        @endforeach
                    </flux:select>

                    <flux:input label="{{ __('Label') }}" wire:model.live.debounce.750ms="form.label" placeholder="Eg. Post title" />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <flux:input label="{{ __('Handle') }}" wire:model="form.handle" placeholder="post_title" />

                    <flux:input label="{{ __('Instructions') }}" wire:model="form.instructions" placeholder="Enter the post title" />
                </div>

                <div class="flex justify-end">
                    <flux:switch label="{{ $form->is_required ? 'Required' : 'Optional' }}" wire:model.live="form.is_required" />
                </div>

                <div class="mt-4 border-t pt-4">
                    <flux:heading size="sm">{{ __('Field Configuration') }}</flux:heading>

                    @includeIf('blueprints.fields.config-' . $form->type, ['index' => $fieldId, 'element' => $form])
                </div>
            </div>
        </div>
    </flux:modal>
</div>
