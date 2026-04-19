<?php

use App\Models\Field;
use Livewire\Attributes\Modelable;
use Livewire\Component;

new class extends Component {
    public Field $field;

    #[Modelable]
    public array $data = [];

    public function mount(Field $field): void
    {
        $this->field = $field;
    }

    public function updatedData(): void
    {
        $this->dispatch('field-data-updated', handle: $this->field->handle, data: $this->data);
    }

    public function addFeatureItem(): void
    {
        $this->data['items'][] = ['icon' => '', 'item_title' => '', 'item_description' => ''];
        $this->dispatch('field-data-updated', handle: $this->field->handle, data: $this->data);
    }

    public function removeFeatureItem(int $index): void
    {
        array_splice($this->data['items'], $index, 1);
        $this->data['items'] = array_values($this->data['items']);
        $this->dispatch('field-data-updated', handle: $this->field->handle, data: $this->data);
    }
};
?>

<div class="space-y-4">
    <flux:input label="{{ __('Title') }}" wire:model.blur="data.title" />

    <div class="space-y-3">
        <flux:label>{{ __('Feature Items') }}</flux:label>

        @foreach ($data['items'] ?? [] as $itemIdx => $item)
            <div wire:key="feature-{{ $field->handle }}-{{ $itemIdx }}" class="space-y-3 rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
                <div class="flex items-center justify-between">
                    <flux:text size="sm" class="font-medium">{{ __('Item :number', ['number' => $itemIdx + 1]) }}</flux:text>
                    <flux:button type="button" variant="ghost" size="sm" icon="trash" wire:click="removeFeatureItem({{ $itemIdx }})" class="text-red-500" />
                </div>
                <flux:input
                    label="{{ __('Icon (Heroicon name)') }}"
                    wire:model.blur="data.items.{{ $itemIdx }}.icon"
                    placeholder="bolt"
                    description="{{ __('Use kebab-case, e.g. light-bulb, arrow-right, check-circle') }}"
                />
                <flux:input label="{{ __('Title') }}" wire:model.blur="data.items.{{ $itemIdx }}.item_title" />
                <flux:textarea label="{{ __('Description') }}" wire:model.blur="data.items.{{ $itemIdx }}.item_description" rows="2" />
            </div>
        @endforeach

        <flux:button type="button" variant="ghost" size="sm" icon="plus" wire:click="addFeatureItem">
            {{ __('Add Feature') }}
        </flux:button>
    </div>
</div>
