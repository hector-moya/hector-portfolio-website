<div class="space-y-4">
    <div class="flex items-center justify-between">
        <div class="space-y-2">
            <flux:input
                wire:model="sections.{{ $section->id }}.name"
                placeholder="{{ __('Section Name') }}"
            />
            <flux:textarea
                wire:model="sections.{{ $section->id }}.instructions"
                placeholder="{{ __('Section Instructions') }}"
                rows="2"
            />
        </div>
        <flux:button
            variant="ghost"
            wire:click="deleteSection('{{ $section->id }}')"
        >
            <flux:icon name="trash" class="w-4 h-4" />
        </flux:button>
    </div>

    {{-- Fields in this section --}}
    <div class="pl-4 space-y-4">
        @foreach($section->elements as $element)
            <div class="border-l-2 border-gray-200 pl-4">
                @include('livewire.blueprints.partials.field', ['element' => $element])
            </div>
        @endforeach

        <flux:button
            variant="ghost"
            wire:click="$emit('openFieldPicker', '{{ $section->id }}')"
            class="ml-4"
        >
            <flux:icon name="plus" class="w-4 h-4 mr-2" />
            {{ __('Add Field') }}
        </flux:button>
    </div>
</div>
