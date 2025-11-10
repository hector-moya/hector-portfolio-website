<div class="space-y-6">
    <flux:heading size="lg">{{ __('Repeater Field Settings') }}</flux:heading>
    <div class="grid grid-cols-2 gap-4">
        <flux:input type="number" label="{{ __('Min items') }}" placeholder="0" wire:model="form.config.min" />
        <flux:input type="number" label="{{ __('Max items') }}" placeholder="∞" wire:model="form.config.max" />
    </div>

    <flux:heading>{{ __('Repeater Blueprint') }}</flux:heading>

    <div class="space-y-3">
        {{-- @forelse ($config['blueprint'] as $index => $field)
            <flux:card wire:key="repeater-nested-{{ $index }}">
                <div class="mb-3 grid grid-cols-2 gap-3">
                    <flux:select label="{{ __('Type') }}" wire:model="form.config.blueprint.{{ $index }}.type">
                        @foreach ($this->fieldTypeOptions as $value => $label)
                            @continue($value === 'repeater')
                            <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:input label="{{ __('Label') }}" wire:model.live.debounce.500ms="form.config.blueprint.{{ $index }}.label" />
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <flux:input label="{{ __('Handle') }}" wire:model="form.config.blueprint.{{ $index }}.handle" />
                    <flux:input label="{{ __('Instructions') }}" wire:model="form.config.blueprint.{{ $index }}.instructions" />
                </div>

                <div class="mt-3 flex items-center justify-between">
                    <flux:switch label="{{ $field['is_required'] ?? false ? 'Required' : 'Optional' }}" wire:model.live="form.config.blueprint.{{ $index }}.is_required" />
                    <flux:button size="xs" variant="danger" wire:click="removeNestedField({{ $index }}, {{ $index }})">
                        {{ __('Remove') }}
                    </flux:button>
                </div>
                <div class="mt-4 border-t pt-3">
                    <x-dynamic-component :component="'blueprints.fields.config-' . $field['type']" :config="$field['config']" />
                </div>
            </flux:card>
        @empty
            <flux:text>{{ __('No nested fields yet.') }}</flux:text>
        @endforelse --}}
        @forelse ($field->children as $nestedField)
            <livewire:blueprints.components.field-card :fieldId="$nestedField->id" :key="'nested-field-' . $nestedField->id" />
        @empty
            <flux:text>{{ __('No nested fields yet.') }}</flux:text>
        @endforelse

        <div class="flex justify-end gap-2">
            <flux:modal.trigger name="select-repeater-nested-field-modal">
                <flux:button size="sm" icon="plus">
                    {{ __('Add nested field') }}
                </flux:button>
            </flux:modal.trigger>
        </div>
        {{-- Select Field Type Modal --}}
        <flux:modal name="select-repeater-nested-field-modal">
            <div class="space-y-6">
                <flux:heading size="lg">{{ __('Select Field Type') }}</flux:heading>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    @foreach ($this->fieldTypeMeta as $type)
                        <flux:card :key="$type['value']" wire:click="addNestedField('{{ $type['value'] }}')" class="cursor-pointer hover:bg-zinc-50 dark:hover:bg-zinc-800">
                            <div class="flex items-center gap-3">
                                <flux:icon name="{{ $type['icon'] }}" class="h-5 w-5" />
                                <flux:text>{{ $type['label'] }}</flux:text>
                            </div>
                        </flux:card>
                    @endforeach
                </div>
            </div>
        </flux:modal>
    </div>
</div>
