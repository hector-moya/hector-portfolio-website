<flux:card class="p-0!">
    <div class="flex items-center justify-between">
        <flux:icon.grip-vertical variant="micro" class="mx-2 transition-opacity duration-200 hover:opacity-45" />
        <flux:separator vertical />
        <div class="m-2 flex flex-grow">
            <flux:modal.trigger name="edit-section-modal-{{ $sectionId }}">
                <flux:button variant="ghost" icon-trailing="pencil" size="sm" tooltip="{{ __('Edit Section') }}">{{ $form->name }}</flux:button>
            </flux:modal.trigger>
        </div>

        <div class="mx-2">
            <flux:modal.trigger name="select-field-modal-{{ $sectionId }}">
                <flux:button icon="plus" size="xs" variant="primary" tooltip="{{ __('Add Field') }}" />
            </flux:modal.trigger>
        </div>
    </div>
    <flux:separator />
    @if (empty($form->fields))
        <div class="rounded-lg border-2 border-dashed border-zinc-300 p-8 text-center dark:border-zinc-600 m-8">
            <flux:text>{{ __('No fields added yet. Click "Plus button" to get started.') }}</flux:text>
        </div>
    @else
        <div class="flex flex-col gap-4 p-6">
            @foreach ($form->fields as $fieldIndex => $field)
            <livewire:blueprints.components.field-card :fieldId="$field->id" :key="'field-'.$fieldIndex" />
            @endforeach
        </div>
    @endif
    <div class="flex justify-center">
    </div>
    {{-- Edit Section Modal --}}
    <flux:modal name="edit-section-modal-{{ $sectionId }}">
        <div class="space-y-6">
            <flux:heading size="lg">{{ __('Edit Section') }}</flux:heading>
            <flux:input label="{{ __('Section Name') }}" placeholder="{{ __('Main Content') }}" wire:model.live.debounce.750ms="form.name" />
            <flux:input label="{{ __('Section Handle') }}" placeholder="{{ __('main_content') }}" wire:model="form.handle" />
            <flux:textarea label="{{ __('Section Instructions') }}" placeholder="{{ __('Instructions for this section...') }}" rows="3" wire:model="form.instructions" />
            <div class="flex justify-end">
                <flux:button type="button" @click="$flux.modal('edit-section-modal-{{ $sectionId }}').close()" variant="outline" class="mr-2">
                    {{ __('Cancel') }}
                </flux:button>
                <flux:button type="button" wire:click="updateSection('{{ $sectionId }}')">
                    {{ __('Update Section') }}
                </flux:button>
            </div>
        </div>
    </flux:modal>
    {{-- Select Field Type Modal --}}
    <flux:modal name="select-field-modal-{{ $sectionId }}">
        <div class="space-y-6">
            <flux:heading size="lg">{{ __('Select Field Type') }}</flux:heading>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                @foreach ($this->fieldTypeMeta as $type)
                    <flux:card :key="$type['value']" wire:click="addField('{{ $type['value'] }}')" class="cursor-pointer hover:bg-zinc-50 dark:hover:bg-zinc-800">
                        <div class="flex items-center gap-3">
                            <flux:icon name="{{ $type['icon'] }}" class="h-5 w-5" />
                            <flux:text>{{ $type['label'] }}</flux:text>
                        </div>
                    </flux:card>
                @endforeach
            </div>
        </div>
    </flux:modal>
</flux:card>
