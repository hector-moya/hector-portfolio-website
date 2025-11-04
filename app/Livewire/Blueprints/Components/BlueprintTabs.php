<?php

namespace App\Livewire\Blueprints\Components;

use App\Livewire\Forms\BlueprintForm;
use App\Models\Tab;
use App\Traits\HasSlug;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Component;

class BlueprintTabs extends Component
{
    use HasSlug;

    public BlueprintForm $form;

    public string $newTabName = '';

    public array $editingTab = [
        'name' => '',
    ];

    public ?int $editingTabId = null;
    public ?int $editingSectionId = null;
    public ?int $currentTabIndex = null;

    public function mount(BlueprintForm $form): void
    {
        $this->form = $form;
    }

    public function addTab(): void
    {
        if (!trim($this->newTabName)) {
            return;
        }

        $this->form->tabs[] = [
            'name' => $this->newTabName,
            'handle' => '',
            'sort_order' => count($this->form->tabs),
            'sections' => [],
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
        $this->form->tabs[$tabIndex]['sections'] ??= [];
        $this->form->tabs[$tabIndex]['sections'][] = [
            'name' => __('New Section'),
            'handle' => '',
            'sort_order' => count($this->form->tabs[$tabIndex]['sections']),
            'instructions' => '',
            'fields' => [],
        ];
    }

    public function updateSection(): void
    {
        Flux::modal('edit-section-modal-' . $this->editingSectionId)->close();
    }

    public function updateTab(): void
    {
        if (!trim($this->editingTab['name'])) {
            return;
        }

        Flux::modal('edit-tab-modal-' . $this->editingTabId)->close();
    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.blueprints.components.blueprint-tabs');
    }
}
