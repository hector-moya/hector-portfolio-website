<div class="space-y-4">
    <div class="grid grid-cols-2 gap-4">
        <flux:input type="number" label="{{ __('Min items') }}"
            wire:model="form.elements.{{ $index }}.config.min" />
        <flux:input type="number" label="{{ __('Max items') }}"
            wire:model="form.elements.{{ $index }}.config.max" />
    </div>

    <flux:heading size="sm">{{ __('Repeater Blueprint') }}</flux:heading>

    @php($nested = $element['config']['blueprint'] ?? [])

    <div class="space-y-3">
        @forelse ($nested as $nIndex => $nestedEl)
            <flux:card wire:key="repeater-{{ $index }}-nested-{{ $nIndex }}">
                <div class="grid grid-cols-2 gap-3 mb-3">
                    <flux:select label="{{ __('Type') }}"
                        wire:model="form.elements.{{ $index }}.config.blueprint.{{ $nIndex }}.type">
                        @foreach ($this->fieldTypeOptions as $value => $label)
                            @continue($value === 'repeater') {{-- defer nested repeaters for v1 --}}
                            <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:input label="{{ __('Label') }}"
                        wire:model.live.debounce.500ms="form.elements.{{ $index }}.config.blueprint.{{ $nIndex }}.label" />
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <flux:input label="{{ __('Handle') }}"
                        wire:model="form.elements.{{ $index }}.config.blueprint.{{ $nIndex }}.handle" />
                    <flux:input label="{{ __('Instructions') }}"
                        wire:model="form.elements.{{ $index }}.config.blueprint.{{ $nIndex }}.instructions" />
                </div>

                <div class="mt-3 flex items-center justify-between">
                    <flux:switch
                        label="{{ ($nestedEl['is_required'] ?? false) ? 'Required' : 'Optional' }}"
                        wire:model.live="form.elements.{{ $index }}.config.blueprint.{{ $nIndex }}.is_required"
                    />
                    <flux:button size="xs" variant="danger"
                        wire:click="removeNestedField({{ $index }}, {{ $nIndex }})">
                        {{ __('Remove') }}
                    </flux:button>
                </div>

                @php($nestedType = $nestedEl['type'] ?? 'text')
                <div class="mt-4 border-t pt-3">
                    @includeIf('blueprints.fields.config-' . $nestedType, [
                        'index' => $index . '.config.blueprint.' . $nIndex,
                        'element' => $nestedEl,
                    ])
                </div>
            </flux:card>
        @empty
            <flux:text>{{ __('No nested fields yet.') }}</flux:text>
        @endforelse

        <div class="flex items-center gap-2">
            <flux:button size="sm" icon="plus" wire:click="addNestedField({{ $index }})">
                {{ __('Add nested field') }}
            </flux:button>
        </div>
    </div>
</div>
