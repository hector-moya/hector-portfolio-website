<div>
    <flux:modal wire:model="showModal">
        <flux:heading size="xl" class="mb-4">{{ __('Add Field') }}</flux:heading>

        <div class="grid grid-cols-2 gap-4">
            @foreach($fieldTypes as $fieldType)
                <flux:button
                    wire:click="addField('{{ $fieldType->value }}')"
                    variant="ghost"
                    class="p-4 text-left"
                >
                    <div class="flex items-start gap-3">
                        <flux:icon name="{{ $fieldType->icon() }}" class="w-6 h-6 shrink-0" />
                        <div>
                            <div class="font-medium">{{ $fieldType->label() }}</div>
                            <div class="text-sm text-gray-500">{{ $fieldType->description() }}</div>
                        </div>
                    </div>
                </flux:button>
            @endforeach
        </div>
    </flux:modal>
</div>
