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
    <flux:editor label="{{ __('Content') }}" wire:model.live.debounce.1000ms="data.content" />
</div>
