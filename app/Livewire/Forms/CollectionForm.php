<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

class CollectionForm extends Form
{
    public ?int $collection_id = null;

    #[Validate('required|string|max:255')]
    public string $name = '';

    public string $slug = '';

    #[Validate('nullable|string|max:1000')]
    public string $description = '';

    #[Validate('nullable|exists:blueprints,id')]
    public ?int $blueprint_id = null;

    #[Validate('boolean')]
    public bool $is_active = true;

    public function rules(): array
    {
        return [
            'slug' => [
                'nullable',
                'string',
                'max:255',
                \Illuminate\Validation\Rule::unique('collections', 'slug')
                    ->ignore($this->collection_id),
            ],
        ];
    }

    public function setCollection($collection): void
    {
        $this->collection_id = $collection->id;
        $this->name = $collection->name;
        $this->slug = $collection->slug;
        $this->description = $collection->description ?? '';
        $this->blueprint_id = $collection->blueprint_id;
        $this->is_active = $collection->is_active;
    }
}
