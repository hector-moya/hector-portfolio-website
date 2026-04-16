<?php

declare(strict_types=1);

namespace App\Livewire\Globals;

use App\Livewire\Forms\GlobalForm;
use App\Models\Blueprint;
use App\Traits\HasSlug;
use Illuminate\View\View;
use Livewire\Component;

final class Create extends Component
{
    use HasSlug;

    public GlobalForm $form;

    public function save(): void
    {
        $globalSet = $this->form->create();

        $this->redirect(route('admin.globals.edit', $globalSet));
    }

    public function updatedFormName(): void
    {
        $this->form->handle = $this->generateSlug($this->form->name);
    }

    public function render(): View
    {
        return view('livewire.globals.create', [
            'blueprints' => Blueprint::all(),
        ]);
    }
}
