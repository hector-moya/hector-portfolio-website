<?php

namespace App\Livewire\Blueprints;

use App\Models\Blueprint;
use App\Models\BlueprintSection;
use App\Models\BlueprintTab;
use Illuminate\Support\Str;
use Livewire\Component;

class TabsManager extends Component
{
    public Blueprint $blueprint;

    public function addTab(): void
    {
        $nextOrder = $this->blueprint->tabs()->max('sort_order') + 1;

        $this->blueprint->tabs()->create([
            'name' => 'New Tab',
            'handle' => 'new_tab_'.Str::random(6),
            'sort_order' => $nextOrder,
        ]);
    }

    public function addSection(?BlueprintTab $tab = null): void
    {
        $nextOrder = $this->blueprint->sections()
            ->when($tab, fn ($q) => $q->where('tab_id', $tab->id))
            ->max('sort_order') + 1;

        $this->blueprint->sections()->create([
            'name' => 'New Section',
            'handle' => 'new_section_'.Str::random(6),
            'tab_id' => $tab?->id,
            'sort_order' => $nextOrder,
        ]);
    }

    public function deleteTab(BlueprintTab $tab): void
    {
        $tab->delete();
    }

    public function deleteSection(BlueprintSection $section): void
    {
        $section->delete();
    }

    public function updateTabOrder($orderedIds): void
    {
        foreach ($orderedIds as $order => $id) {
            \App\Models\BlueprintTab::query()->where('id', $id)->update(['sort_order' => $order]);
        }
    }

    public function updateSectionOrder($orderedIds): void
    {
        foreach ($orderedIds as $order => $id) {
            \App\Models\BlueprintSection::query()->where('id', $id)->update(['sort_order' => $order]);
        }
    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.blueprints.tabs-manager', [
            'tabs' => $this->blueprint->tabs()->with('sections.elements')->get(),
            'rootSections' => $this->blueprint->sections()->whereNull('tab_id')->with('elements')->get(),
        ]);
    }
}
