<?php

namespace App\Livewire\Blueprints;

use App\Enums\FieldType;
use App\Models\BlueprintElement;
use App\Models\BlueprintSection;
use Illuminate\Support\Str;
use Livewire\Component;

class FieldPicker extends Component
{
    public ?string $sectionId = null;
    public bool $showModal = false;

    protected $listeners = ['openFieldPicker'];

    public function openFieldPicker(string $sectionId): void
    {
        $this->sectionId = $sectionId;
        $this->showModal = true;
    }

    public function addField(string $type): void
    {
        $section = BlueprintSection::findOrFail($this->sectionId);
        $nextOrder = $section->elements()->max('sort_order') + 1;

        $element = new BlueprintElement([
            'type' => $type,
            'label' => Str::title($type),
            'handle' => Str::slug($type) . '_' . Str::random(6),
            'instructions' => '',
            'required' => false,
            'sort_order' => $nextOrder,
        ]);

        $section->elements()->save($element);

        $this->showModal = false;
        $this->emit('fieldAdded');
    }

    public function render()
    {
        return view('livewire.blueprints.field-picker', [
            'fieldTypes' => FieldType::cases(),
        ]);
    }
}
