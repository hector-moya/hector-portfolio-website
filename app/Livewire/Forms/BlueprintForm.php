<?php

namespace App\Livewire\Forms;

use App\Enums\FieldType;
use App\Livewire\Actions\Blueprints\CreateBlueprint;
use App\Livewire\Actions\Blueprints\DeleteBlueprint;
use App\Livewire\Actions\Blueprints\UpdateBlueprint;
use App\Models\Blueprint;
use App\Services\FieldTypeRegistry;
use Flux\Flux;
use Illuminate\Support\Str;
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
                'nullable', 'string', 'max:255',
                function ($attribute, $value, $fail): void {
                    $handles = array_column($this->elements, 'handle');
                    if ($value && count(array_keys($handles, $value)) > 1) {
                        $fail(__('Handles must be unique within the blueprint.'));
                    }
                },
            ],
            'elements.*.instructions' => 'nullable|string',
            'elements.*.is_required' => 'boolean',
            'elements.*.config' => 'array',
        ];
    }

    public function validate($rules = null, $messages = [], $attributes = [])
    {
        $validated = parent::validate($rules, $messages, $attributes);

        // Per-type config validation (including nested repeater blueprints)
        $registry = app(FieldTypeRegistry::class);
        foreach ($this->elements as $index => $element) {
            $registry->validateConfig($element, $index);

            if (($element['type'] ?? null) === FieldType::Repeater->value) {
                foreach (($element['config']['blueprint'] ?? []) as $nestedIndex => $nested) {
                    // validate nested config recursively
                    $registry->validateConfig($nested, $nestedIndex);
                }
            }
        }

        return $validated;
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
        $defaultConfig = app(\App\Services\FieldTypeRegistry::class)->defaultConfigFor($type);

        $this->elements[] = [
            'type' => $type,
            'label' => '',
            'handle' => '',
            'instructions' => '',
            'is_required' => false,
            'config' => $defaultConfig,
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

    public function addNestedField(int $parentIndex, string $type = 'text'): void
    {
        // Ensure array scaffolding exists
        $this->elements[$parentIndex]['config'] ??= [];
        $this->elements[$parentIndex]['config']['blueprint'] ??= [];

        // Default config for the chosen type
        $defaults = app(FieldTypeRegistry::class)->defaultConfigFor($type);

        $this->elements[$parentIndex]['config']['blueprint'][] = [
            'type' => $type,
            'label' => '',
            'handle' => '',
            'instructions' => '',
            'is_required' => false,
            'config' => $defaults,
        ];
    }

    public function removeNestedField(int $parentIndex, int $childIndex): void
    {
        if (! isset($this->elements[$parentIndex]['config']['blueprint'][$childIndex])) {
            return;
        }

        unset($this->elements[$parentIndex]['config']['blueprint'][$childIndex]);
        $this->elements[$parentIndex]['config']['blueprint'] = array_values(
            $this->elements[$parentIndex]['config']['blueprint']
        );
    }

    /**
     * Generic helpers for option-based types (select/radio) while editing blueprint.
     */
    public function addOption(int $index): void
    {
        $this->elements[$index]['config'] ??= [];
        $this->elements[$index]['config']['options'] ??= [];
        $this->elements[$index]['config']['options'][] = ['value' => '', 'label' => ''];
    }

    public function removeOption(int $index, int $optIndex): void
    {
        if (! isset($this->elements[$index]['config']['options'][$optIndex])) {
            return;
        }

        unset($this->elements[$index]['config']['options'][$optIndex]);
        $this->elements[$index]['config']['options'] = array_values(
            $this->elements[$index]['config']['options']
        );
    }
}
