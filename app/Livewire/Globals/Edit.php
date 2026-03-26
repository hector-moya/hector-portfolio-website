<?php

declare(strict_types=1);

namespace App\Livewire\Globals;

use App\Livewire\Forms\GlobalForm;
use App\Models\Blueprint;
use App\Models\GlobalSet;
use Illuminate\View\View;
use Livewire\Component;

class Edit extends Component
{
    public GlobalForm $form;

    public GlobalSet $globalSet;

    public function mount(GlobalSet $globalSet): void
    {
        $this->globalSet = $globalSet;
        $this->form->setGlobalSet($globalSet);
    }

    public function save(): void
    {
        $this->form->update($this->globalSet->id);
    }

    public function render(): View
    {
        return view('livewire.globals.edit', [
            'blueprints' => Blueprint::all(),
        ]);
    }
}
