<?php

namespace App\Livewire\Forms;

use App\Enums\FieldType;
use App\Models\Section;
use App\Services\FieldTypeRegistry;
use Illuminate\Support\Collection;
use Livewire\Attributes\Validate;
use Livewire\Form;

class SectionForm extends Form
{
    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|string|max:255')]
    public string $handle = '';

    #[Validate('nullable|string')]
    public string $instructions = '';

    public ?int $blueprint_id = null;

    public ?int $tab_id = null;

    public ?int $sort_order = null;

    public ?Collection $fields = null;

    public function setSection(?int $sectionId): void
    {
        $section = Section::query()->findOrFail($sectionId)->load('fields:id,section_id');

        $this->name = $section->name;
        $this->handle = $section->handle;
        $this->instructions = $section->instructions;
        $this->blueprint_id = $section->blueprint_id;
        $this->tab_id = $section->tab_id;
        $this->sort_order = $section->sort_order;
        $this->fields = $section->fields;
    }

    public function update(int $sectionId): Section
    {
        $this->validate();

        $section = Section::query()->findOrFail($sectionId);

        $section->update([
            'name' => $this->name,
            'handle' => $this->handle,
            'instructions' => $this->instructions,
            'sort_order' => $this->sort_order,
        ]);

        return $section;
    }

    public function addField(int $sectionId, string $type): void
    {
        $defaultConfig = app(FieldTypeRegistry::class)->defaultConfigFor($type);

        $section = Section::query()->findOrFail($sectionId);
        $section->fields()->create([
            'type' => $type,
            'blueprint_id' => $section->blueprint_id,
            'label' => FieldType::from($type)->defaultLabel(),
            'handle' => '',
            'instructions' => '',
            'is_required' => false,
            'config' => $defaultConfig,
        ]);
    }

    public function destroy(int $sectionId): void
    {
        Section::query()->findOrFail($sectionId)->delete();
    }
}
