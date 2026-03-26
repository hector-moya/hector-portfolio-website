<?php

declare(strict_types=1);

namespace App\Livewire\Globals;

use App\Models\Blueprint;
use App\Models\GlobalSet;
use App\Models\GlobalVariable;
use Illuminate\View\View;
use Livewire\Component;

// TODO: This component should use GlobalForm (app/Livewire/Forms/GlobalForm.php) instead of
//       carrying raw properties + a protected rules() method + inline persistence.
//       Once GlobalForm gains setGlobalSet(), update(), and destroy() methods (see TODOs in GlobalForm),
//       refactor as follows:
//   - Replace the $name, $handle, $blueprint_id, $variables properties with `public GlobalForm $form`.
//   - Call $this->form->setGlobalSet($globalSet) in mount().
//   - Replace the inline $this->validate() + $globalSet->update() + GlobalVariable upsert loop in save()
//     with a single $this->form->update($globalSet->id) call.
//   - Remove the protected rules() method entirely; validation belongs in the form.
//   This aligns the component with the established pattern (compare Users/Edit.php, Taxonomies/Edit.php).
class Edit extends Component
{
    public GlobalSet $globalSet;

    public string $name;

    public string $handle;

    public ?int $blueprint_id = null;

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
            GlobalVariable::query()->updateOrCreate(['global_set_id' => $this->globalSet->id, 'handle' => $handle], ['value' => $value]);
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
