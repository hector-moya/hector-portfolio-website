<?php

namespace App\Livewire\Blueprints\Components;

use App\Services\FieldTypeRegistry;
use App\Enums\FieldType;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Ramsey\Uuid\Uuid;
use Livewire\Attributes\Reactive;
use Livewire\Component;

class SectionCard extends Component
{
    public array $section = [];
    public string $tabId = '';
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

        $field = [
            'id' => Uuid::uuid4()->toString(),
            'name' => FieldType::from($type)->defaultLabel(),
            'type' => $type,
            'icon' => FieldType::from($type)->icon(),
            'label' => FieldType::from($type)->defaultLabel(),
            'handle' => '',
            'instructions' => '',
            'is_required' => false,
            'config' => $defaultConfig,
        ];

        $this->dispatch('field-added', ['section' => $this->section, 'tabId' => $this->tabId, 'field' => $field]);

        Flux::modal('select-field-modal-'.$this->section['id'])->close();
    }
    public function updateSection(string $id): void
    {
        $this->dispatch('update-section', ['section' => $this->section, 'tabId' => $this->tabId]);
        Flux::modal('edit-section-modal-' . $id)->close();
    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.blueprints.components.section-card');
    }
}
