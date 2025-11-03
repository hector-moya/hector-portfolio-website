<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use App\Models\Tab;
use Livewire\Form;

class TabForm extends Form
{
    #[Validate('required|string|max:255')]
    public string $name = '';
    #[Validate('required|string|max:255')]
    public string $handle = '';
    #[Validate('required|integer|min:0')]
    public int $sort_order = 0;

    public array $sections = [];

    public function setTab(?int $tabId): void
    {
        $tab = Tab::find($tabId) ?? null;
        $this->name = $tab?->name ?? '';
        $this->handle = $tab?->handle ?? '';
        $this->sort_order = $tab?->sort_order ?? 0;
        $this->sections = $tab ? $tab->sections()->orderBy('sort_order')->get()->toArray() : [['id' => 0, 'name' => '', 'handle' => '', 'instructions' => '', 'sort_order' => 0]];
    }
}
