<div>
    <flux:card class="!p-0">
        <div class="flex items-center justify-between">
            <flux:icon.grip-vertical wire:sort:handle variant="micro" class="mx-2 cursor-move transition-opacity duration-200 hover:opacity-45" />
            <flux:separator vertical />
            <div class="m-2 flex flex-grow items-center">
                <flux:modal.trigger name="field-config-{{ $fieldId }}">
                    <flux:button size="xs" variant="ghost" icon="{{ $form->icon }}" tooltip="{{ __('Configure ') . $form->label }}">{{ $form->label }}</flux:button>
                </flux:modal.trigger>
            </div>
            <div class="mx-2 flex">
                <flux:button icon="clipboard-document-list" size="xs" variant="ghost" tooltip="{{ __('Duplicate Field') }}" wire:click="duplicate" />
                @if ($form->parent_id)
                    <flux:modal.trigger name="confirm-remove-nested-field-{{ $fieldId }}">
                        <flux:button icon="trash" size="xs" variant="ghost" tooltip="{{ __('Remove Nested Field') }}" />
                    </flux:modal.trigger>
                @else
                    <flux:modal.trigger name="confirm-remove-field-{{ $fieldId }}">
                        <flux:button icon="trash" size="xs" variant="ghost" tooltip="{{ __('Remove Field') }}" />
                    </flux:modal.trigger>
                @endif
            </div>
        </div>
    </flux:card>

    {{-- Confirm remove field --}}
    @if ($form->parent_id)
        <flux:modal name="confirm-remove-nested-field-{{ $fieldId }}" class="min-w-sm">
            <div class="space-y-4">
                <flux:heading size="lg">{{ __('Remove Field') }}</flux:heading>
                <flux:text>{{ __('Are you sure you want to remove this nested field? This cannot be undone.') }}</flux:text>
                <div class="flex justify-end gap-3">
                    <flux:button @click="$flux.modal('confirm-remove-nested-field-{{ $fieldId }}').close()" variant="outline">{{ __('Cancel') }}</flux:button>
                    <flux:button wire:click="$parent.removeNestedField({{ $fieldId }})" variant="danger">{{ __('Remove') }}</flux:button>
                </div>
            </div>
        </flux:modal>
    @else
        <flux:modal name="confirm-remove-field-{{ $fieldId }}" class="min-w-sm">
            <div class="space-y-4">
                <flux:heading size="lg">{{ __('Remove Field') }}</flux:heading>
                <flux:text>{{ __('Are you sure you want to remove "') }}{{ $form->label }}{{ __('"? This cannot be undone.') }}</flux:text>
                <div class="flex justify-end gap-3">
                    <flux:button @click="$flux.modal('confirm-remove-field-{{ $fieldId }}').close()" variant="outline">{{ __('Cancel') }}</flux:button>
                    <flux:button wire:click="removeField" variant="danger">{{ __('Remove') }}</flux:button>
                </div>
            </div>
        </flux:modal>
    @endif

    <flux:modal name="field-config-{{ $fieldId }}" :closable="false" variant="{{ $form->parent_id ? 'flyout' : 'default' }}" class="min-w-140">
        <div class="flex items-start gap-4">
            <div class="flex-1 space-y-4">
                <div class="flex justify-between">
                    <div class="flex items-center gap-2">
                        <flux:icon name="{{ $form->icon }}" variant="micro" />
                        <flux:heading size="lg">{{ __('Configure ' . ucfirst($form->type) . ' Field') }}</flux:heading>
                    </div>
                    <flux:switch label="{{ $form->is_required ? 'Required' : 'Optional' }}" wire:model.live="form.is_required" />
                </div>
                <flux:separator />
                <div class="grid grid-cols-2 gap-4">
                    <flux:select label="{{ __('Type') }}" wire:model="form.type">
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

                <div class="space-y-4">
                    <x-dynamic-component component="blueprints.fields.config-{{ $form->type }}" :config="$form->config" :children="$form->children" />
                </div>

                <div class="flex justify-between">
                    <flux:button type="button" @click="$flux.modal('field-config-{{ $fieldId }}').close()" variant="outline" class="mr-2">
                        {{ __('Cancel') }}
                    </flux:button>
                    <flux:button type="button" variant="primary" wire:click="updateField('{{ $fieldId }}')">
                        {{ __('Update Field') }}
                    </flux:button>
                </div>
            </div>
        </div>
    </flux:modal>
</div>
