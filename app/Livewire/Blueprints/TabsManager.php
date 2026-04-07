<?php

namespace App\Livewire\Blueprints;

use App\Models\Blueprint;
use App\Models\Section;
use App\Models\Tab;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
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

    public function addSection(?Tab $tab = null): void
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

    public function deleteTab(Tab $tab): void
    {
        $tab->delete();
    }

    public function deleteSection(Section $section): void
    {
        $section->delete();
    }

    public function updateTabOrder($orderedIds): void
    {
        foreach ($orderedIds as $order => $id) {
            Tab::query()->where('id', $id)->update(['sort_order' => $order]);
        }
    }

    public function updateSectionOrder($orderedIds): void
    {
        foreach ($orderedIds as $order => $id) {
            Section::query()->where('id', $id)->update(['sort_order' => $order]);
        }
    }

    public function render(): View|Factory
    {
        return view('livewire.blueprints.tabs-manager', [
            'tabs' => $this->blueprint->tabs()->with('sections.elements')->get(),
            'rootSections' => $this->blueprint->sections()->whereNull('tab_id')->with('elements')->get(),
        ]);
    }
}
