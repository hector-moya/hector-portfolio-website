<?php

namespace App\Livewire\Blueprints\Components;

use App\Services\FieldTypeRegistry;
use App\Enums\FieldType;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Component;

class SectionCard extends Component
{
    public array $section = [];
    public array $fields = [];
    public function mount(): void
    {
        $this->fields = $this->section['fields'] ?? [];
    }

    #[Computed]
    public function fieldTypeMeta(): array
    {
        return app(FieldTypeRegistry::class)->all();
    }

    public function addField(string $type): void
    {
        $defaultConfig = app(FieldTypeRegistry::class)->defaultConfigFor($type);

        $this->section['fields'][] = [
            'name' => FieldType::from($type)->defaultLabel(),
            'type' => $type,
            'icon' => FieldType::from($type)->icon(),
            'label' => FieldType::from($type)->defaultLabel(),
            'handle' => '',
            'instructions' => '',
            'is_required' => false,
            'config' => $defaultConfig,
        ];

        Flux::modal('select-field-modal-'.$this->section['id'])->close();
    }


    public function removeField(int $index): void
    {
        unset($this->fields[$index]);
        $this->fields = array_values($this->fields);
    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.blueprints.components.section-card');
    }
}
