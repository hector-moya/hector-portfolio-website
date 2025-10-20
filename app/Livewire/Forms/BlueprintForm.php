<?php

namespace App\Livewire\Forms;

use App\Livewire\Actions\Blueprints\UpdateBlueprint;
use App\Livewire\Actions\Blueprints\DeleteBlueprint;
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
            'elements.*.handle' => 'nullable|string|max:255',
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

    public function create() {}

    public function update(int $blueprintId): Blueprint
    {
        return app(UpdateBlueprint::class)->execute([
            'id' => $blueprintId,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'is_active' => $this->is_active,
            'elements' => $this->elements,
        ]);
    }

    public function addElement(): void
    {
        $this->elements[] = [
            'type' => 'text',
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
            heading:'Blueprint Deleted',
            text:'The blueprint has been successfully deleted.',
            variant:'success',
        );

    }
}
