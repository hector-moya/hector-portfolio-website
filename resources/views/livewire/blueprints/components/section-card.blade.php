<flux:card class="p-0!">
    <div class="flex items-center justify-between">
        <flux:icon.grip-vertical variant="micro" class="mx-2 transition-opacity duration-200 hover:opacity-45" />
        <flux:separator vertical />
        <div class="m-2 flex flex-grow">
            <flux:modal.trigger name="edit-section-modal-{{ $section['id'] }}">
                <flux:button variant="ghost" icon-trailing="pencil" size="sm" tooltip="{{ __('Edit Section') }}">{{ $section['name'] }}</flux:button>
            </flux:modal.trigger>
        </div>

        <div class="mx-2">
            <flux:modal.trigger name="select-field-modal-{{ $section['id'] }}">
                <flux:button icon="plus" size="xs" variant="primary" tooltip="{{ __('Add Field') }}" />
            </flux:modal.trigger>
        </div>
    </div>
    <flux:separator />
    @if (empty($section['fields']))
        <div class="rounded-lg border-2 border-dashed border-zinc-300 p-8 text-center dark:border-zinc-600 m-8">
            <flux:text>{{ __('No fields added yet. Click "Plus button" to get started.') }}</flux:text>
        </div>
    @else
        <div class="flex flex-col gap-4 p-6">
            @foreach ($section['fields'] as $fieldIndex => $field)
                <flux:card :key="'field-'.$fieldIndex" class="!p-0">
                    <div class="flex items-center justify-between">
                        <flux:icon.grip-vertical variant="micro" class="mx-2 transition-opacity duration-200 hover:opacity-45" />
                        <flux:separator vertical />
                        <div class="m-2 flex flex-grow items-center">
                            <flux:button size="xs" variant="ghost" icon="{{ $field['icon'] }}" tooltip="{{ __('Configure ') . $field['name'] }}">{{ $field['name'] }}</flux:button>
                        </div>
                        <div class="mx-2 flex">
                            <flux:button icon="clipboard-document-list" size="xs" variant="ghost" tooltip="{{ __('Duplicate Field') }}" />
                            <flux:button icon="trash" size="xs" variant="ghost" tooltip="{{ __('Remove Field') }}" wire:click="removeField({{ $fieldIndex }})" />
                        </div>
                    </div>
                </flux:card>
                {{-- <div class="flex items-start gap-4" wire:key="element-{{ $index }}">
                                                    <div class="flex-1 space-y-4">
                                                        <div class="grid grid-cols-2 gap-4">
                                                            <flux:select label="{{ __('Type') }}" wire:model="form.elements.{{ $index }}.type">
                                                                @foreach ($this->fieldTypeOptions as $value => $label)
                                                                    <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
                                                                @endforeach
                                                            </flux:select>

                                                            <flux:input label="{{ __('Label') }}" wire:model.live.debounce.750ms="form.elements.{{ $index }}.label" placeholder="Eg. Post title" />
                                                        </div>

                                                        <div class="grid grid-cols-2 gap-4">
                                                            <flux:input label="{{ __('Handle') }}" wire:model="form.elements.{{ $index }}.handle" placeholder="post_title" />

                                                            <flux:input label="{{ __('Instructions') }}" wire:model="form.elements.{{ $index }}.instructions" placeholder="Enter the post title" />
                                                        </div>

                                                        <div class="flex justify-end">
                                                            <flux:switch label="{{ $form->elements[$index]['is_required'] ? 'Required' : 'Optional' }}" wire:model.live="form.elements.{{ $index }}.is_required" />
                                                        </div>

                                                        <div class="mt-4 border-t pt-4">
                                                            <flux:heading size="sm">{{ __('Field Configuration') }}</flux:heading>

                                                            @php($type = $form->elements[$index]['type'] ?? 'text')

                                                            @includeIf('blueprints.fields.config-' . $type, ['index' => $index, 'element' => $form->elements[$index]])
                                                        </div>
                                                    </div>

                                                    <flux:button type="button" icon="trash" wire:click="removeElement({{ $index }})" size="sm" variant="danger" />
                                                </div> --}}
            @endforeach
        </div>
    @endif
    <div class="flex justify-center">
    </div>
    {{-- Edit Section Modal --}}
    <flux:modal name="edit-section-modal-{{ $section['id'] }}">
        <div class="space-y-6">
            <flux:heading size="lg">{{ __('Edit Section') }}</flux:heading>
            <flux:input label="{{ __('Section Name') }}" placeholder="{{ __('Main Content') }}" wire:model="section.name" />
            <flux:textarea label="{{ __('Section Instructions') }}" placeholder="{{ __('Instructions for this section...') }}" rows="3" wire:model="section.instructions" />
            <div class="flex justify-end">
                <flux:button type="button" @click="$flux.modal('edit-section-modal-{{ $section['id'] }}').close()" variant="outline" class="mr-2">
                    {{ __('Cancel') }}
                </flux:button>
                <flux:button type="button" wire:click="updateSection('{{ $section['id'] }}')">
                    {{ __('Update Section') }}
                </flux:button>
            </div>
        </div>
    </flux:modal>
    {{-- Select Field Type Modal --}}
    <flux:modal name="select-field-modal-{{ $section['id'] ?? '' }}">
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
