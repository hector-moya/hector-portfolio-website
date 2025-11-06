<?php

namespace App\Livewire\Blueprints\Components;

use App\Traits\HasSlug;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Contracts\View\Factory;
use Ramsey\Uuid\Uuid;
use Livewire\Component;
use Livewire\Attributes\Reactive;

class BlueprintTabs extends Component
{
    use HasSlug;

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

        $this->dispatch('tab-added', ['tab' => end($this->tabs)]);
        Flux::modal('add-tab-modal')->close();
    }

    public function removeTab(int $index): void
    {
        unset($this->tabs[$index]);
        $this->dispatch('tab-removed', $this->tabs);
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
