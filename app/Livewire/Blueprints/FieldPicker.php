<?php

declare(strict_types=1);

namespace App\Livewire\Blueprints;

use App\Enums\FieldType;
use App\Models\Field;
use App\Models\Section;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Component;

final class FieldPicker extends Component
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
        $section = Section::query()->findOrFail($this->sectionId);
        $nextOrder = $section->fields()->max('sort_order') + 1;

        $element = new Field([
            'type' => $type,
            'label' => Str::title($type),
            'handle' => Str::slug($type).'_'.Str::random(6),
            'instructions' => '',
            'required' => false,
            'sort_order' => $nextOrder,
        ]);

        $section->fields()->save($element);

        $this->showModal = false;
        $this->dispatch('fieldAdded');
    }

    public function render(): View|Factory
    {
        return view('livewire.blueprints.field-picker', [
            'fieldTypes' => FieldType::cases(),
        ]);
    }
}
