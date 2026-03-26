<?php

declare(strict_types=1);

namespace App\Livewire\Forms;

use App\Actions\Globals\CreateGlobal;
use App\Actions\Globals\DeleteGlobal;
use App\Actions\Globals\UpdateGlobal;
use App\Models\GlobalSet;
use Flux\Flux;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Livewire\Form;

class GlobalForm extends Form
{
    public ?int $global_set_id = null;

    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|string|max:255')]
    public string $handle = '';

    #[Validate('required|integer|exists:blueprints,id')]
    public ?int $blueprint_id = null;

    /** @var array<string, mixed> */
    public array $variables = [];

    public function rules(): array
    {
        return [
            'handle' => [
                'required',
                'string',
                'max:255',
                Rule::unique('global_sets', 'handle')->ignore($this->global_set_id),
            ],
        ];
    }

    public function setGlobalSet(GlobalSet $globalSet): void
    {
        $this->global_set_id = $globalSet->id;
        $this->name = $globalSet->name;
        $this->handle = $globalSet->handle;
        $this->blueprint_id = $globalSet->blueprint_id;
        $this->variables = $globalSet->variables->pluck('value', 'handle')->toArray();
    }

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

    public function update(int $globalSetId): GlobalSet
    {
        $this->validate();

        $globalSet = app(UpdateGlobal::class)->handle([
            'id' => $globalSetId,
            'name' => $this->name,
            'handle' => $this->handle,
            'blueprint_id' => $this->blueprint_id,
            'variables' => $this->variables,
        ]);

        Flux::toast(
            heading: __('Success'),
            text: __('Global set updated successfully.'),
            variant: 'success'
        );

        return $globalSet;
    }

    public function destroy(int $globalSetId): void
    {
        app(DeleteGlobal::class)->handle($globalSetId);

        Flux::toast(
            heading: __('Success'),
            text: __('Global set deleted successfully.'),
            variant: 'success'
        );
    }
}
