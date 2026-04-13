<?php

declare(strict_types=1);

namespace App\Livewire\LandingPages;

use App\Ai\Agents\LandingPageWizardAgent;
use App\Enums\SectionType;
use App\Livewire\Actions\Blueprints\CreateBlueprint;
use App\Livewire\Actions\Collections\CreateCollection;
use App\Livewire\Actions\CreateEntry;
use App\Models\Collection;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

final class AiWizard extends Component
{
    public string $step = 'describe';

    public string $name = '';

    public string $slug = '';

    public string $description = '';

    /** @var array<int, array<string, mixed>> */
    #[Locked]
    public array $proposal = [];

    public function mount(): void
    {
        $this->authorize('create', Collection::class);
    }

    public function updatedName(): void
    {
        $this->slug = Str::slug($this->name);
    }

    public function generate(): void
    {
        $this->validate([
            'name' => 'required|string|min:2|max:255',
            'slug' => 'required|string|max:255|unique:collections,slug',
            'description' => 'required|string|min:10|max:1000',
        ]);

        $response = LandingPageWizardAgent::make()->prompt($this->description);

        $this->proposal = $response['sections'];
        $this->step = 'review';
    }

    public function removeSection(int $index): void
    {
        if ($this->step !== 'review') {
            return;
        }

        $sections = $this->proposal;
        array_splice($sections, $index, 1);
        $this->proposal = array_values($sections);
    }

    public function back(): void
    {
        $this->resetValidation();
        $this->step = 'describe';
    }

    public function save(): void
    {
        $this->validate([
            'name' => 'required|string|min:2|max:255',
            'slug' => 'required|string|max:255|unique:collections,slug',
            'description' => 'required|string|min:10|max:1000',
        ]);

        DB::transaction(function (): void {
            // 1. Create collection (blueprint_id set after blueprint is created)
            $collection = resolve(CreateCollection::class)->execute([
                'name' => $this->name,
                'slug' => $this->slug,
                'is_active' => true,
                'settings' => ['type' => 'single'],
            ]);

            // 2. Build one blueprint field per section from the proposal
            $blueprintFields = [];
            foreach ($this->proposal as $idx => $section) {
                $sectionType = SectionType::from($section['type']);
                $blueprintFields[] = [
                    'label' => $sectionType->defaultLabel(),
                    'handle' => $section['type'].'_'.($idx + 1),
                    'type' => $sectionType->value,
                    'instructions' => '',
                    'is_required' => false,
                    'config' => $sectionType->defaultConfig(),
                    'sortOrder' => $idx,
                ];
            }

            // 3. Create blueprint with one field per section
            $blueprint = resolve(CreateBlueprint::class)->create([
                'name' => $this->name.' Blueprint',
                'slug' => $this->slug.'-blueprint',
                'description' => '',
                'is_active' => true,
                'tabs' => [
                    [
                        'name' => 'Content',
                        'handle' => 'content',
                        'sortOrder' => 0,
                        'sections' => [
                            [
                                'name' => 'Page Builder',
                                'handle' => 'page_builder',
                                'instructions' => '',
                                'sortOrder' => 0,
                                'fields' => $blueprintFields,
                            ],
                        ],
                    ],
                ],
            ]);

            // 4. Link blueprint to collection
            $collection->update(['blueprint_id' => $blueprint->id]);

            // 5. Build entry elements — one per section, value is the section data array
            $fieldsValues = [];
            $fields = $blueprint->tabs->first()->sections->first()->fields->sortBy('order')->values();

            foreach ($this->proposal as $idx => $section) {
                $field = $fields->get($idx);
                if ($field === null) {
                    continue;
                }

                $fieldsValues[] = [
                    'field_id' => $field->id,
                    'handle' => $field->handle,
                    'type' => $field->type,
                    'value' => $section['data'],
                    'children' => [],
                ];
            }

            // 6. Create entry with one element per section
            $entry = resolve(CreateEntry::class)->handle([
                'title' => $this->name,
                'slug' => $this->slug,
                'blueprint_id' => $blueprint->id,
                'status' => 'draft',
                'fieldsValues' => $fieldsValues,
            ]);

            $this->redirect(route('entries.edit', $entry), navigate: true);
        });
    }

    #[Layout('components.layouts.app')]
    public function render(): View|Factory
    {
        return view('livewire.landing-pages.ai-wizard');
    }
}
