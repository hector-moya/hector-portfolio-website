<?php

declare(strict_types=1);

namespace App\Livewire\Globals;

use App\Models\Blueprint;
use App\Models\GlobalSet;
use Illuminate\View\View;
use Livewire\Component;

class Edit extends Component
{
    public GlobalSet $globalSet;

    public string $name;

    public string $handle;

    public ?string $blueprint_id = null;

    public array $variables = [];

    public function mount(GlobalSet $globalSet): void
    {
        $this->globalSet = $globalSet;
        $this->name = $globalSet->name;
        $this->handle = $globalSet->handle;
        $this->blueprint_id = $globalSet->blueprint_id;
        $this->variables = $globalSet->variables->pluck('value', 'handle')->toArray();
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'handle' => ['required', 'string', 'max:255', 'unique:global_sets,handle,'.$this->globalSet->id],
            'blueprint_id' => ['nullable', 'exists:blueprints,id'],
            'variables' => ['array'],
        ];
    }

    public function updatedBlueprintId(): void
    {
        $this->globalSet->blueprint_id = $this->blueprint_id;
    }

    public function save(): void
    {
        $this->validate();

        $this->globalSet->update([
            'name' => $this->name,
            'handle' => $this->handle,
            'blueprint_id' => $this->blueprint_id,
        ]);

        foreach ($this->variables as $handle => $value) {
            \App\Models\GlobalVariable::query()->updateOrCreate(['global_set_id' => $this->globalSet->id, 'handle' => $handle], ['value' => $value]);
        }

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Global set updated successfully.',
        ]);
    }

    public function render(): View
    {
        return view('livewire.globals.edit', [
            'blueprints' => Blueprint::all(),
        ]);
    }
}
