<?php

declare(strict_types=1);

namespace App\Livewire\Blueprints\Components;

use App\Models\Section;
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

    public function reorderTabs(int $id, int $position): void
    {
        $tabs = Tab::query()->where('blueprint_id', $this->blueprintId)->orderBy('sort_order')->get();

        $tab = $tabs->firstWhere('id', $id);
        $rest = $tabs->reject(fn ($t): bool => $t->id === $id)->values();
        $rest->splice($position, 0, [$tab]);

        foreach ($rest as $index => $t) {
            $t->update(['sort_order' => $index]);
        }
    }

    public function reorderSections(int $id, int $position): void
    {
        $section = Section::query()->findOrFail($id);
        $tabId = $section->tab_id;

        $sections = Section::query()
            ->where('blueprint_id', $this->blueprintId)
            ->where('tab_id', $tabId)
            ->orderBy('sort_order')
            ->get();

        $rest = $sections->reject(fn ($s): bool => $s->id === $id)->values();
        $rest->splice($position, 0, [$section]);

        foreach ($rest as $index => $s) {
            $s->update(['sort_order' => $index]);
        }
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
