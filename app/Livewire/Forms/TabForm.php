<?php

namespace App\Livewire\Forms;

use App\Models\Tab;
use App\Traits\Blueprints\Sections;
use Livewire\Attributes\Validate;
use Livewire\Form;

class TabForm extends Form
{
    use Sections;

    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|string|max:255')]
    public string $handle = '';

    #[Validate('required|integer|min:0')]
    public int $sort_order = 0;

    public array $sections = [];

    public function setTab(?int $tabId): void
    {
        $tab = Tab::query()->find($tabId) ?? null;
        $this->name = $tab?->name ?? '';
        $this->handle = $tab?->handle ?? '';
        $this->sort_order = $tab?->sort_order ?? 0;
        $this->sections = $tab ? $tab->sections()->orderBy('sort_order')->get()->toArray() : [['id' => 0, 'name' => '', 'handle' => '', 'instructions' => '', 'sort_order' => 0]];
    }

    public function create(int $blueprintId): Tab
    {
        $this->validate();

        $tab = Tab::create([
            'blueprint_id' => $blueprintId,
            'name' => $this->name,
            'handle' => $this->handle,
            'sort_order' => $this->sort_order,
        ]);

        $this->createNewSection($tab);

        return $tab;
    }

    public function createNewSection(Tab $tab): void
    {
        $tab->sections()->create($this->newSection([
            'blueprint_id' => (int) $tab->getAttribute('blueprint_id'),
            'tab_id' => (int) $tab->getKey(),
        ]));

    }
}
