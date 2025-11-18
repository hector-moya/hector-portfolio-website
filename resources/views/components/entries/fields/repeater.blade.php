@php
    $handle = $field->handle;
    $items = $form->fieldValues[$handle]['items'] ?? [];
    $children = $field->children;
    $min = $field->config['min'] ?? 0;
    $max = $field->config['max'] ?? null;
    $canAdd = $max === null || count($items) < $max;
    $canRemove = count($items) > $min;
@endphp

<flux:card class="space-y-4">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="md">{{ $field->label }}</flux:heading>
            @if ($field->instructions)
                <flux:description>{{ $field->instructions }}</flux:description>
            @endif
        </div>
        <flux:button size="xs" icon="plus" wire:click="addRepeaterItem('{{ $handle }}')" :disabled="!$canAdd">
            {{ __('Add item') }}
        </flux:button>
    </div>

    @if ($min > 0 || $max !== null)
        <flux:text size="xs" class="opacity-70">
            @if ($min > 0 && $max !== null)
                {{ __('Required: :min - :max items', ['min' => $min, 'max' => $max]) }}
            @elseif($min > 0)
                {{ __('Minimum: :min items', ['min' => $min]) }}
            @elseif($max !== null)
                {{ __('Maximum: :max items', ['max' => $max]) }}
            @endif
            ({{ __('Current: :count', ['count' => count($items)]) }})
        </flux:text>
    @endif

    @forelse ($form->fieldValues[$handle]['items'] as $index => $item)
        <flux:card class="space-y-3" wire:key="rep-{{ $handle }}-{{ $index }}">
            <div class="flex items-start justify-between">
                <flux:heading size="sm">{{ __('Item ') . $index + 1 }}</flux:heading>
                <flux:button
                    size="xs"
                    variant="danger"
                    icon="trash"
                    wire:click="removeRepeaterItem('{{ $handle }}', {{ $index }})"
                    :disabled="!$canRemove"
                >
                    {{ __('Remove') }}
                </flux:button>
            </div>

            @foreach ($children as $childField)
                    <x-dynamic-component :component="'entries.fields.' . $childField->type" :$index :field="$childField" :parentHandle="$handle" wire:key="child-field-{{ $index }}-{{ $childField->id }}"/>
            @endforeach
        </flux:card>
    @empty
        <flux:text>{{ __('No items yet.') }}</flux:text>
    @endforelse
</flux:card>

<flux:error name="form.fieldValues.{{ $handle }}.items" />
