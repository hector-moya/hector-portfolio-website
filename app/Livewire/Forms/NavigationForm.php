<?php

namespace App\Livewire\Forms;

use App\Livewire\Actions\Navigation\CreateNavigation;
use App\Livewire\Actions\Navigation\UpdateNavigation;
use App\Models\Navigation;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Livewire\Form;

class NavigationForm extends Form
{
    public ?int $navigation_id = null;

    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|string|max:255')]
    public string $handle = '';

    #[Validate('nullable|string|max:500')]
    public string $description = '';

    #[Validate('required|boolean')]
    public bool $is_active = true;

    public function rules(): array
    {
        return [
            'handle' => [
                'required',
                'string',
                'max:255',
                Rule::unique('navigations', 'handle')->ignore($this->navigation_id),
            ],
        ];
    }

    public function updatedName(): void
    {
        if ($this->navigation_id === null) {
            $this->handle = Str::slug($this->name);
        }
    }

    public function setNavigation(Navigation $navigation): void
    {
        $this->navigation_id = $navigation->id;
        $this->name = $navigation->name;
        $this->handle = $navigation->handle;
        $this->description = $navigation->description ?? '';
        $this->is_active = $navigation->is_active;
    }

    public function create(): Navigation
    {
        $this->validate();

        return app(CreateNavigation::class)->create([
            'name' => $this->name,
            'handle' => $this->handle,
            'description' => $this->description,
            'is_active' => $this->is_active,
        ]);
    }

    public function update(Navigation $navigation): Navigation
    {
        $this->validate();

        return app(UpdateNavigation::class)->update($navigation, [
            'name' => $this->name,
            'handle' => $this->handle,
            'description' => $this->description,
            'is_active' => $this->is_active,
        ]);
    }
}
