<?php

namespace App\Livewire\Blueprints;

use App\Enums\FieldType;
use App\Livewire\Forms\BlueprintForm;
use App\Services\FieldTypeRegistry;
use App\Traits\HasSlug;
use Flux\Flux;
use Ramsey\Uuid\Uuid;
use App\Models\Blueprint;
use App\Traits\Blueprints\Tabs;
use App\Traits\Blueprints\Sections;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\Component;

class Create extends Component
{
    use HasSlug, Tabs, Sections;

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
        $this->form->create();

        $this->redirect(route('blueprints.index'), navigate: true);
    }

    #[Title('Create Blueprint')]
    public function render(): View|Factory
    {
        return view('livewire.blueprints.create', [
            'tabs' => $this->form->tabs,
        ]);
    }
}
