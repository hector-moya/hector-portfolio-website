<?php

namespace App\Livewire\Blueprints\Components;

use App\Livewire\Forms\TabForm;
use App\Models\Tab;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Component;

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

    public function mount(): void
    {
        $this->form->setTab(null);
    }

    public function addTab(): void
    {
        $id = $this->generateSlug($this->newTabName) ?: 'tab-'.str()->random();
        $this->tabs[$id] = $this->newTabName !== '' && $this->newTabName !== '0' ? $this->newTabName : 'Tab #'.(count($this->tabs) + 1);
        $this->newTabName = '';
        Flux::modal('add-tab-modal')->close();
    }

    #[Computed]
    public function sections(): array
    {
        return $this->form->sections;
    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.blueprints.components.blueprint-tabs');
    }
}
