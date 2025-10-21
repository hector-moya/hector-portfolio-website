<?php

namespace App\Livewire\Forms;

use App\Livewire\Actions\Blueprints\CreateBlueprint;
use App\Livewire\Actions\Blueprints\DeleteBlueprint;
use App\Livewire\Actions\Blueprints\UpdateBlueprint;
use Illuminate\Support\Str;
use App\Models\Blueprint;
use Flux\Flux;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Livewire\Form;

class BlueprintForm extends Form
{
    public ?int $blueprint_id = null;

    #[Validate('required|string|max:255')]
    public string $name = '';

    public string $slug = '';

    #[Validate('nullable|string|max:1000')]
    public string $description = '';

    #[Validate('boolean')]
    public bool $is_active = true;

    public array $elements = [];

    public function rules(): array
    {
        return [
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('blueprints', 'slug')->ignore($this->blueprint_id),
            ],
            'elements.*.type' => 'required|string',
            'elements.*.label' => 'required|string|max:255',
            'elements.*.handle' => [
                'nullable',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    $handles = array_column((array) $this->elements, 'handle');
                    if ($value && count(array_keys($handles, $value)) > 1) {
                        $fail(__('Handles must be unique within the blueprint.'));
                    }
                },
            ],
            'elements.*.instructions' => 'nullable|string',
            'elements.*.is_required' => 'boolean',
        ];
    }

    public function setBlueprint($blueprint): void
    {
        $this->blueprint_id = $blueprint->id;
        $this->name = $blueprint->name;
        $this->slug = $blueprint->slug;
        $this->description = $blueprint->description ?? '';
        $this->is_active = $blueprint->is_active;

        $this->elements = $blueprint->elements->map(fn ($element): array => [
            'type' => $element->type,
            'label' => $element->label,
            'handle' => $element->handle,
            'instructions' => $element->instructions ?? '',
            'is_required' => $element->is_required,
            'config' => $element->config ?? [],
        ])->toArray();
    }

    public function create(): Blueprint
    {
        $this->validate();

        $blueprint = (new CreateBlueprint)->create(
            blueprintData: [
                'name' => $this->name,
                'slug' => $this->slug,
                'description' => $this->description,
                'is_active' => $this->is_active,
                'elements' => $this->elements,
            ]);

        Flux::toast(
            heading: 'Blueprint Created',
            text: 'The blueprint has been successfully created.',
            variant: 'success',
        );

        $this->reset(['name', 'slug', 'description', 'is_active', 'elements']);

        return $blueprint;
    }

    public function update(int $blueprintId): Blueprint
    {
        $this->validate();

        $blueprint = app(UpdateBlueprint::class)->update(
            blueprintData: [
                'id' => $blueprintId,
                'name' => $this->name,
                'slug' => $this->slug,
                'description' => $this->description,
                'is_active' => $this->is_active,
                'elements' => $this->elements,
            ]);

        Flux::toast(
            heading: 'Blueprint Updated',
            text: 'The blueprint has been successfully updated.',
            variant: 'success',
        );

        return $blueprint;
    }

    public function addElement(string $type): void
    {
        $this->elements[] = [
            'type' => $type,
            'label' => '',
            'handle' => '',
            'instructions' => '',
            'is_required' => false,
            'config' => [],
        ];
    }

    public function removeElement(int $index): void
    {
        unset($this->elements[$index]);
        $this->elements = array_values($this->elements);
    }

    public function destroy(int $blueprintId): void
    {
        $blueprint = Blueprint::query()->findOrFail($blueprintId);

        (new DeleteBlueprint)->execute($blueprint);

        Flux::toast(
            heading: 'Blueprint Deleted',
            text: 'The blueprint has been successfully deleted.',
            variant: 'success',
        );

    }

    public function generateSlug(string $slug): string
    {
        return Str::slug($slug);
    }

    public function updateHandleFromLabel(int $index): void
    {
        $this->elements[$index]['handle'] = $this->generateSlug($this->elements[$index]['label']);
    }
}
