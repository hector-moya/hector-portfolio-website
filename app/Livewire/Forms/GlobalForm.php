<?php

declare(strict_types=1);

namespace App\Livewire\Forms;

use App\Actions\Globals\CreateGlobal;
use App\Models\GlobalSet;
use Flux\Flux;
use Livewire\Attributes\Validate;
use Livewire\Form;

// TODO: This form only handles creation. Add the following to complete the CRUD pattern:
//   1. A $global_set_id property (nullable int) to track the model being edited.
//   2. A setGlobalSet(GlobalSet $globalSet) method to populate fields for edit.
//   3. Move the unique handle validation out of the #[Validate] attribute and into a rules() method
//      that uses Rule::unique('global_sets', 'handle')->ignore($this->global_set_id), so the rule
//      correctly ignores the current record during updates.
//   4. An update(int $globalSetId) method that calls an UpdateGlobal action
//      (app/Livewire/Actions/Globals/UpdateGlobal.php or app/Actions/Globals/UpdateGlobal.php).
//      The action should handle GlobalSet update + GlobalVariable upsert loop, replacing the
//      inline logic currently in Globals/Edit.php.
//   5. A destroy(int $globalSetId) method that calls a DeleteGlobal action.
//   Without these, Globals/Edit.php cannot use this form and is forced to do all persistence inline.
class GlobalForm extends Form
{
    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|string|max:255|unique:global_sets,handle')]
    public string $handle = '';

    #[Validate('required|integer|exists:blueprints,id')]
    public ?int $blueprint_id = null;

    public function create(): GlobalSet
    {
        $this->validate();

        $globalSet = app(CreateGlobal::class)->handle([
            'name' => $this->name,
            'handle' => $this->handle,
            'blueprint_id' => $this->blueprint_id,
        ]);

        Flux::toast(
            heading: __('Success'),
            text: __('Global set created successfully.'),
            variant: 'success'
        );

        return $globalSet;
    }
}
