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
};
?>

<div class="space-y-4">
    <flux:textarea label="{{ __('Content') }}" wire:model.blur="data.content" rows="6" />
    <flux:select label="{{ __('Alignment') }}" wire:model.live="data.alignment">
        <flux:select.option value="left">{{ __('Left') }}</flux:select.option>
        <flux:select.option value="center">{{ __('Center') }}</flux:select.option>
        <flux:select.option value="right">{{ __('Right') }}</flux:select.option>
    </flux:select>
</div>
