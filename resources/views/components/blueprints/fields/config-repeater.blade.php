<div class="space-y-6">
    <flux:heading size="lg">{{ __('Repeater Field Settings') }}</flux:heading>
    <div class="grid grid-cols-2 gap-4">
        <flux:input type="number" label="{{ __('Min items') }}" placeholder="0" wire:model="form.config.min" />
        <flux:input type="number" label="{{ __('Max items') }}" placeholder="∞" wire:model="form.config.max" />
    </div>

    <flux:heading>{{ __('Repeater Blueprint') }}</flux:heading>

    <div class="space-y-3">
        @forelse ($children as $nestedField)
            <livewire:blueprints.components.field-card :fieldId="$nestedField->id" :key="'nested-field-' . $nestedField->id" />
        @empty
            <flux:text>{{ __('No nested fields yet.') }}</flux:text>
        @endforelse

        <div class="flex justify-end gap-2">
            <flux:modal.trigger name="select-repeater-nested-field-modal">
                <flux:button size="sm" icon="plus" variant="primary">
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
