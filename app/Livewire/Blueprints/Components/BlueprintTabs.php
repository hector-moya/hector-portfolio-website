<?php

namespace App\Livewire\Blueprints\Components;

use App\Livewire\Forms\BlueprintForm;
use App\Traits\HasSlug;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Contracts\View\Factory;
use Ramsey\Uuid\Uuid;
use Livewire\Component;

class BlueprintTabs extends Component
{
    use HasSlug;

    public BlueprintForm $form;

    public string $newTabName = '';

    public array $editingTab = [
        'name' => '',
    ];

    public array $tabs = [];

    public ?int $editingTabId = null;
    public ?int $editingSectionId = null;
    public ?int $currentTabIndex = null;

    public function mount(): void
    {
    }

    public function addTab(): void
    {
        if (! trim($this->newTabName)) {
            return;
        }

        $this->tabs[] = [
            'name' => $this->newTabName,
            'handle' => '',
            'sort_order' => count($this->tabs),
            'sections' => [[
                'id' => UUID::uuid4()->toString(),
                'name' => __('New Section'),
                'handle' => '',
                'sort_order' => count($this->tabs[count($this->tabs) - 1]['sections']),
                'instructions' => '',
                'fields' => [],
            ],],
        ];

        $this->newTabName = '';
        Flux::modal('add-tab-modal')->close();
    }

    public function removeTab(int $index): void
    {
        unset($this->form->tabs[$index]);
        $this->form->tabs = array_values($this->form->tabs);
    }

    public function addSection(int $tabIndex): void
    {
        $this->tabs[$tabIndex]['sections'][] = [
            'id' => UUID::uuid4()->toString(),
            'name' => __('New Section'),
            'handle' => '',
            'sort_order' => count($this->tabs[$tabIndex]['sections']),
            'instructions' => '',
            'fields' => [],
        ];
    }

    public function updateTab(): void
    {
        if (! trim($this->editingTab['name'])) {
            return;
        }

        Flux::modal('edit-tab-modal-'.$this->editingTabId)->close();
    }

    public function render(): View|Factory
    {
        return view('livewire.blueprints.components.blueprint-tabs');
    }
}
