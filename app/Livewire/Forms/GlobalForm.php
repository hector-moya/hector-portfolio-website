<?php

declare(strict_types=1);

namespace App\Livewire\Forms;

use App\Actions\Globals\CreateGlobal;
use App\Models\GlobalSet;
use Flux\Flux;
use Livewire\Attributes\Validate;
use Livewire\Form;

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
