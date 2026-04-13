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
};
?>

<div class="space-y-4">
    <flux:input label="{{ __('Title') }}" wire:model.blur="data.title" />
    <flux:textarea label="{{ __('Content') }}" wire:model.blur="data.content" rows="3" />
    <div class="grid grid-cols-2 gap-4">
        <flux:input label="{{ __('CTA Text') }}" wire:model.blur="data.cta_text" />
        <flux:input label="{{ __('CTA URL') }}" wire:model.blur="data.cta_url" placeholder="/" />
    </div>
</div>
