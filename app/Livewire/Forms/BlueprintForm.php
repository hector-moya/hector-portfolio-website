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

    public array $tabs = [];

    public function rules(): array
    {
        return [
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('blueprints', 'slug')->ignore($this->blueprint_id),
            ],
            'tabs' => 'array',
            'tabs.*.name' => 'required|string|max:255',
            'tabs.*.handle' => 'nullable|string|max:255',
            'tabs.*.sections' => 'array',
            'tabs.*.sections.*.name' => 'required|string|max:255',
            'tabs.*.sections.*.handle' => 'nullable|string|max:255',
            'tabs.*.sections.*.instructions' => 'nullable|string',
            'tabs.*.sections.*.fields' => 'array',
            'tabs.*.sections.*.fields.*.name' => 'required|string|max:255',
            'tabs.*.sections.*.fields.*.handle' => [
                'nullable',
                'string',
                'max:255',
                function ($attribute, $value, $fail): void {
                    $handles = collect($this->tabs)
                        ->pluck('sections.*.fields.*.handle')
                        ->flatten()
                        ->filter()
                        ->toArray();
                    if ($value && count(array_keys($handles, $value)) > 1) {
                        $fail(__('Handles must be unique within the blueprint.'));
                    }
                },
            ],
            'tabs.*.sections.*.fields.*.type' => 'required|string',
            'tabs.*.sections.*.fields.*.instructions' => 'nullable|string',
            'tabs.*.sections.*.fields.*.required' => 'boolean',
            'tabs.*.sections.*.fields.*.config' => 'array',
        ];
    }

    public function validate($rules = null, $messages = [], $attributes = [])
    {
        $validated = parent::validate($rules, $messages, $attributes);

        // Per-type config validation
        $registry = app(FieldTypeRegistry::class);

        foreach ($this->tabs as $tab) {
            foreach ($tab['sections'] ?? [] as $section) {
                foreach ($section['fields'] ?? [] as $index => $field) {
                    $registry->validateConfig($field, $index);

                    if (($field['type'] ?? null) === FieldType::Repeater->value) {
                        foreach (($field['config']['blueprint'] ?? []) as $nestedIndex => $nested) {
                            // validate nested config recursively
                            $registry->validateConfig($nested, $nestedIndex);
                        }
                    }
                }
            }
        }

        return $validated;
    }

    public function setBlueprint(Blueprint $blueprint): void
    {
        $this->blueprint_id = $blueprint->id;
        $this->name = $blueprint->name;
        $this->slug = $blueprint->slug;
        $this->description = $blueprint->description ?? '';
        $this->is_active = $blueprint->is_active;

        $this->tabs = $blueprint->tabs->map(function ($tab) {
            return [
                'name' => $tab->name,
                'handle' => $tab->handle,
                'sort_order' => $tab->sort_order,
                'sections' => $tab->sections->map(function ($section) {
                    return [
                        'name' => $section->name,
                        'handle' => $section->handle,
                        'sort_order' => $section->sort_order,
                        'instructions' => $section->instructions,
                        'fields' => $section->fields->map(function ($field) {
                            return [
                                'name' => $field->name,
                                'handle' => $field->handle,
                                'type' => $field->type,
                                'sort_order' => $field->sort_order,
                                'instructions' => $field->instructions,
                                'required' => $field->required,
                                'config' => $field->config ?? [],
                                'validation' => $field->validation ?? [],
                            ];
                        })->all(),
                    ];
                })->all(),
            ];
        })->all();
    }

    public function create(): Blueprint
    {
        $this->validate();

        $tabs = collect($this->tabs)->map(function (array $tabData) {
            return TabDto::fromArray([
                'name' => $tabData['name'],
                'handle' => $tabData['handle'] ?? Str::slug($tabData['name']),
                'sort_order' => $tabData['sort_order'] ?? 0,
                'sections' => array_map(function (array $sectionData) {
                    return SectionDto::fromArray([
                        'name' => $sectionData['name'],
                        'handle' => $sectionData['handle'] ?? Str::slug($sectionData['name']),
                        'sort_order' => $sectionData['sort_order'] ?? 0,
                        'instructions' => $sectionData['instructions'] ?? null,
                        'fields' => array_map(function (array $fieldData) {
                            return FieldDto::fromArray([
                                'name' => $fieldData['name'],
                                'handle' => $fieldData['handle'] ?? Str::slug($fieldData['name']),
                                'type' => $fieldData['type'],
                                'sort_order' => $fieldData['sort_order'] ?? 0,
                                'instructions' => $fieldData['instructions'] ?? null,
                                'required' => $fieldData['required'] ?? false,
                                'config' => $fieldData['config'] ?? [],
                                'validation' => $fieldData['validation'] ?? [],
                            ]);
                        }, $sectionData['fields'] ?? []),
                    ]);
                }, $tabData['sections'] ?? []),
            ]);
        })->all();

        $blueprint = (new CreateBlueprint)->create([
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'is_active' => $this->is_active,
            'tabs' => $tabs,
        ]);

        Flux::toast(
            heading: 'Blueprint Created',
            text: 'The blueprint has been successfully created.',
            variant: 'success',
        );

        $this->reset(['name', 'slug', 'description', 'is_active', 'tabs']);

        return $blueprint;
    }

    public function update(int $blueprintId): Blueprint
    {
        $this->validate();

        $tabs = collect($this->tabs)->map(function (array $tabData) {
            return TabDto::fromArray([
                'name' => $tabData['name'],
                'handle' => $tabData['handle'] ?? Str::slug($tabData['name']),
                'sort_order' => $tabData['sort_order'] ?? 0,
                'sections' => array_map(function (array $sectionData) {
                    return SectionDto::fromArray([
                        'name' => $sectionData['name'],
                        'handle' => $sectionData['handle'] ?? Str::slug($sectionData['name']),
                        'sort_order' => $sectionData['sort_order'] ?? 0,
                        'instructions' => $sectionData['instructions'] ?? null,
                        'fields' => array_map(function (array $fieldData) {
                            return FieldDto::fromArray([
                                'name' => $fieldData['name'],
                                'handle' => $fieldData['handle'] ?? Str::slug($fieldData['name']),
                                'type' => $fieldData['type'],
                                'sort_order' => $fieldData['sort_order'] ?? 0,
                                'instructions' => $fieldData['instructions'] ?? null,
                                'required' => $fieldData['required'] ?? false,
                                'config' => $fieldData['config'] ?? [],
                                'validation' => $fieldData['validation'] ?? [],
                            ]);
                        }, $sectionData['fields'] ?? []),
                    ]);
                }, $tabData['sections'] ?? []),
            ]);
        })->all();

        $blueprint = app(UpdateBlueprint::class)->update([
                'id' => $blueprintId,
                'name' => $this->name,
                'slug' => $this->slug,
                'description' => $this->description,
                'is_active' => $this->is_active,
                'tabs' => $tabs,
        ]);

        Flux::toast(
            heading: 'Blueprint Updated',
            text: 'The blueprint has been successfully updated.',
            variant: 'success',
        );
        return $blueprint;
    }

    public function addField(string $type): void
    {
        $defaultConfig = app(FieldTypeRegistry::class)->defaultConfigFor($type);

        $this->fields[] = [
            'type' => $type,
            'icon' => FieldType::from($type)->icon(),
            'label' => FieldType::from($type)->defaultLabel(),
            'handle' => '',
            'instructions' => '',
            'is_required' => false,
            'config' => $defaultConfig,
        ];
    }

    public function removeField(int $index): void
    {
        unset($this->fields[$index]);
        $this->fields = array_values($this->fields);
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
        $this->fields[$index]['handle'] = $this->generateSlug($this->fields[$index]['label']);
    }

    public function addNestedField(int $parentIndex, string $type = 'text'): void
    {
        // Ensure array scaffolding exists
        $this->fields[$parentIndex]['config'] ??= [];
        $this->fields[$parentIndex]['config']['blueprint'] ??= [];

        // Default config for the chosen type
        $defaults = app(FieldTypeRegistry::class)->defaultConfigFor($type);

        $this->fields[$parentIndex]['config']['blueprint'][] = [
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
        if (! isset($this->fields[$parentIndex]['config']['blueprint'][$childIndex])) {
            return;
        }

        unset($this->fields[$parentIndex]['config']['blueprint'][$childIndex]);
        $this->fields[$parentIndex]['config']['blueprint'] = array_values(
            $this->fields[$parentIndex]['config']['blueprint']
        );
    }

    /**
     * Generic helpers for option-based types (select/radio) while editing blueprint.
     */
    public function addOption(int $index): void
    {
        $this->fields[$index]['config'] ??= [];
        $this->fields[$index]['config']['options'] ??= [];
        $this->fields[$index]['config']['options'][] = ['value' => '', 'label' => ''];
    }

    public function removeOption(int $index, int $optIndex): void
    {
        if (! isset($this->fields[$index]['config']['options'][$optIndex])) {
            return;
        }

        unset($this->fields[$index]['config']['options'][$optIndex]);
        $this->fields[$index]['config']['options'] = array_values(
            $this->fields[$index]['config']['options']
        );
    }
}
