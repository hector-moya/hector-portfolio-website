<?php

namespace App\Livewire\Forms;

use App\Enums\FieldType;
use App\Livewire\Actions\Blueprints\CreateBlueprint;
use App\Livewire\Actions\Blueprints\DeleteBlueprint;
use App\Livewire\Actions\Blueprints\UpdateBlueprint;
use App\Models\Blueprint;
use App\Services\FieldTypeRegistry;
use App\Traits\Blueprints\Tabs;
use Flux\Flux;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Livewire\Form;

class BlueprintForm extends Form
{
    use Tabs;

    public ?int $blueprint_id = null;

    #[Validate('required|string|max:255')]
    public string $name = '';

    public string $slug = '';

    #[Validate('nullable|string|max:1000')]
    public string $description = '';

    #[Validate('boolean')]
    public bool $is_active = true;

    #[Validate('nullable|string')]
    public string $detail_template = '';

    public array $fields = [];

    public array $sections = [];

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
            'fields.*.type' => 'required|string',
            'fields.*.label' => 'required|string|max:255',
            'fields.*.handle' => [
                'nullable', 'string', 'max:255',
                function ($attribute, $value, $fail): void {
                    $handles = array_column($this->fields, 'handle');
                    if ($value && count(array_keys($handles, $value)) > 1) {
                        $fail(__('Handles must be unique within the blueprint.'));
                    }
                },
            ],
            'fields.*.instructions' => 'nullable|string',
            'fields.*.is_required' => 'boolean',
            'fields.*.config' => 'array',
        ];
    }

    public function validate($rules = null, $messages = [], $attributes = [])
    {
        $validated = parent::validate($rules, $messages, $attributes);

        // Per-type config validation (including nested repeater blueprints)
        $registry = app(FieldTypeRegistry::class);
        foreach ($this->fields as $index => $element) {
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
        if (! $blueprint) {
            return;
        }

        $this->blueprint_id = $blueprint->id;
        $this->name = $blueprint->name;
        $this->slug = $blueprint->slug;
        $this->description = $blueprint->description ?? '';
        $this->is_active = $blueprint->is_active;
        $this->detail_template = $blueprint->settings['detail_template'] ?? '';

        $this->tabs = $blueprint->tabs->map(fn ($tab): array => [
            'id' => $tab->id,
        ])->all();
    }

    public function create(): Blueprint
    {
        $tabs = $this->initializeTabs();
        // TODO: Uncomment $this->validate() once the create flow populates name/slug before saving.
        //       Currently hardcodes 'New Blueprint' / 'new-blueprint' as a workaround; this should instead
        //       use $this->name and $this->slug so the validation covers actual user-supplied values.
        // $this->validate();

        $blueprint = (new CreateBlueprint)->create(
            blueprintData: [
                'name' => 'New Blueprint',
                'slug' => 'new-blueprint',
                'description' => '',
                'is_active' => false,
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

        $blueprint = app(UpdateBlueprint::class)->update(
            blueprintData: [
                'id' => $blueprintId,
                'name' => $this->name,
                'slug' => $this->slug,
                'description' => $this->description,
                'is_active' => $this->is_active,
                'settings' => array_filter(['detail_template' => $this->detail_template ?: null]),
                'tabs' => $this->tabs,
            ]);

        Flux::toast(
            heading: 'Blueprint Updated',
            text: 'The blueprint has been successfully updated.',
            variant: 'success',
        );

        return $blueprint;
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
}
