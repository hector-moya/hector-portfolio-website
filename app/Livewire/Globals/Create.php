<?php

declare(strict_types=1);

namespace App\Livewire\Globals;

use App\Livewire\Forms\GlobalForm;
use App\Models\Blueprint;
use App\Traits\HasSlug;
use Illuminate\View\View;
use Livewire\Component;

class Create extends Component
{
    use HasSlug;

    public GlobalForm $form;

    public string $name = '';

    public string $handle = '';

    public ?string $blueprint_id = null;

    protected $rules = [
        'name' => ['required', 'string', 'max:255'],
        'handle' => ['required', 'string', 'max:255', 'unique:global_sets,handle'],
        'blueprint_id' => ['nullable', 'exists:blueprints,id'],
    ];

    public function save(): void
    {
        $globalSet = $this->form->create();

        $this->redirect(route('admin.globals.edit', $globalSet));
    }

    public function updatedName(): void
    {
        $this->handle = $this->generateSlug($this->form->name);
    }

    public function render(): View
    {
        return view('livewire.globals.create', [
            'blueprints' => Blueprint::all(),
        ]);
    }
}
