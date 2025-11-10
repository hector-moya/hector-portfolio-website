<?php

namespace App\Livewire\Blueprints;

use App\Livewire\Forms\BlueprintForm;
use App\Models\Blueprint;
use App\Traits\Blueprints\Sections;
use App\Traits\Blueprints\Tabs;
use App\Traits\HasSlug;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

class Edit extends Component
{
    use HasSlug, Sections, Tabs;

    public ?Blueprint $blueprint = null;

    public BlueprintForm $form;

    public function mount(): void
    {
        $this->form->setBlueprint($this->blueprint);
    }

    public function updatedFormName(): void
    {
        $this->form->slug = $this->form->generateSlug($this->form->name);
    }

    public function save(): void
    {
        $this->form->update($this->form->blueprint_id);

        $this->redirect(route('blueprints.index'), navigate: true);
    }

    #[Title('Edit Blueprint')]
    public function render(): View|Factory
    {
        return view('livewire.blueprints.edit', [
            'tabs' => $this->form->tabs,
        ]);
    }
}
