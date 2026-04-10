<?php

declare(strict_types=1);

namespace App\Livewire\Blueprints\Components;

use App\Livewire\Forms\TabForm;
use App\Models\Tab;
use App\Traits\HasSlug;
use Flux\Flux;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Livewire\Component;

final class BlueprintTabs extends Component
{
    use HasSlug;

    public TabForm $form;

    public ?int $blueprintId = null;

    public ?Collection $tabs = null;

    #[On(['tabs-updated', 'section-removed'])]
    public function mount(): void
    {
        $this->tabs = Tab::query()->where('blueprint_id', $this->blueprintId)->with('sections:id,tab_id')->get();
    }

    public function openEditModal(int $tabId): void
    {
        $tab = $this->tabs->firstWhere('id', $tabId);
        if (! $tab) {
            return;
        }

        $this->form->name = $tab->name;
        $this->form->handle = $tab->handle;

        Flux::modal('edit-tab-modal-'.$tab->id)->show();
    }

    public function updatedFormName(): void
    {
        $this->form->handle = $this->generateSlug($this->form->name);
    }

    public function addTab(): void
    {
        $blueprintId = $this->tabs->first()->blueprint_id;
        $this->form->create($blueprintId);

        $this->dispatch('tabs-updated');

        Flux::modal('add-tab-modal')->close();
    }

    public function updateTab(int $tabId): void
    {
        $tab = $this->tabs->firstWhere('id', $tabId);
        if (! $tab) {
            return;
        }

        $tab->update([
            'name' => $this->form->name,
            'handle' => $this->form->handle,
        ]);

        Flux::modal('edit-tab-modal-'.$tabId)->close();
    }

    public function deleteTab(int $tabId): void
    {
        $tab = $this->tabs->firstWhere('id', $tabId);
        if (! $tab) {
            return;
        }

        $tab->delete();

        $this->dispatch('tabs-updated');

        Flux::modal('edit-tab-modal-'.$tabId)->close();
    }

    public function addSection(int $tabId): void
    {
        $tab = $this->tabs->firstWhere('id', $tabId);

        if (! $tab) {
            return;
        }

        $this->form->createNewSection($tab);
    }

    public function render(): View|Factory
    {
        return view('livewire.blueprints.components.blueprint-tabs');
    }
}
