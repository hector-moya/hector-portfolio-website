<?php

declare(strict_types=1);

namespace App\Livewire\Globals;

use App\Models\Blueprint;
use App\Models\GlobalSet;
use Illuminate\View\View;
use Livewire\Component;

class Create extends Component
{
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
        $this->validate();

        $set = GlobalSet::create([
            'name' => $this->name,
            'handle' => $this->handle,
            'blueprint_id' => $this->blueprint_id,
        ]);

        $this->redirect(route('admin.globals.edit', $set));
    }

    public function render(): View
    {
        return view('livewire.globals.create', [
            'blueprints' => Blueprint::all(),
        ]);
    }
}
