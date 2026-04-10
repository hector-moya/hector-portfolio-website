<?php

declare(strict_types=1);

namespace App\Livewire\Blueprints;

use App\Ai\Agents\BlueprintWizardAgent;
use App\Enums\FieldType;
use App\Livewire\Actions\Blueprints\CreateBlueprint;
use App\Models\Blueprint;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

final class AiWizard extends Component
{
    public string $step = 'describe';

    public string $description = '';

    /** @var array<string, mixed> */
    public array $proposal = [];

    public function mount(): void
    {
        $this->authorize('create', Blueprint::class);
    }

    public function generate(): void
    {
        $this->validate(['description' => 'required|string|min:10|max:1000']);

        $response = BlueprintWizardAgent::make()->prompt($this->description);

        $this->proposal = [
            'name' => $response['name'],
            'slug' => $response['slug'],
            'description' => $response['description'],
            'tabs' => $response['tabs'],
        ];

        $this->step = 'review';
    }

    public function removeTab(int $tabIndex): void
    {
        $tabs = $this->proposal['tabs'];
        array_splice($tabs, $tabIndex, 1);
        $this->proposal['tabs'] = array_values($tabs);
    }

    public function removeSection(int $tabIndex, int $sectionIndex): void
    {
        $sections = $this->proposal['tabs'][$tabIndex]['sections'];
        array_splice($sections, $sectionIndex, 1);
        $this->proposal['tabs'][$tabIndex]['sections'] = array_values($sections);
    }

    public function removeField(int $tabIndex, int $sectionIndex, int $fieldIndex): void
    {
        $fields = $this->proposal['tabs'][$tabIndex]['sections'][$sectionIndex]['fields'];
        array_splice($fields, $fieldIndex, 1);
        $this->proposal['tabs'][$tabIndex]['sections'][$sectionIndex]['fields'] = array_values($fields);
    }

    public function save(): void
    {
        $tabs = [];

        foreach ($this->proposal['tabs'] as $tabIndex => $tab) {
            $sections = [];

            foreach ($tab['sections'] as $sectionIndex => $section) {
                $fields = [];

                foreach ($section['fields'] as $fieldIndex => $field) {
                    $type = FieldType::tryFrom($field['type']) ?? FieldType::Text;

                    $fields[] = [
                        'label' => $field['label'],
                        'handle' => $field['handle'],
                        'type' => $type->value,
                        'instructions' => $field['instructions'] ?? '',
                        'is_required' => $field['is_required'] ?? false,
                        'config' => $type->defaultConfig(),
                        'sortOrder' => $fieldIndex,
                    ];
                }

                $sections[] = [
                    'name' => $section['name'],
                    'handle' => $section['handle'] ?? Str::slug($section['name']),
                    'instructions' => '',
                    'fields' => $fields,
                    'sortOrder' => $sectionIndex,
                ];
            }

            $tabs[] = [
                'name' => $tab['name'],
                'handle' => $tab['handle'] ?? Str::slug($tab['name']),
                'sections' => $sections,
                'sortOrder' => $tabIndex,
            ];
        }

        $blueprint = resolve(CreateBlueprint::class)->create([
            'name' => $this->proposal['name'],
            'slug' => $this->proposal['slug'],
            'description' => $this->proposal['description'],
            'is_active' => false,
            'tabs' => $tabs,
        ]);

        $this->redirect(route('blueprints.edit', $blueprint), navigate: true);
    }

    #[Layout('components.layouts.app')]
    public function render(): View|Factory
    {
        return view('livewire.blueprints.ai-wizard');
    }
}
