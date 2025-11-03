<?php

namespace App\Livewire\Blueprints\Components;

use Livewire\Component;
use App\Models\Tab;
use Livewire\Attributes\Computed;
use Flux\Flux;
use App\Livewire\Forms\TabForm;

class BlueprintTabs extends Component
{
    public int $tabId;
    public array $tabs = [
        1 => 'Main',
        2 => 'Side',
    ];
    public string $newTabName = '';

    public TabForm $form;

    public Tab $tab;

    public function mount()
    {
        // $this->form->setTab($this->tabId);
    }
    public function addTab(): void
    {
        $id = $this->generateSlug($this->newTabName) ?: 'tab-'.str()->random();
        $this->tabs[$id] = $this->newTabName ?: 'Tab #'.(count($this->tabs) + 1);
        $this->newTabName = '';
        Flux::modal('add-tab-modal')->close();
    }

    #[Computed]
    public function sections(): array
    {
        return $this->form->sections;
    }

    public function render()
    {
        return view('livewire.blueprints.components.blueprint-tabs');
    }
}
