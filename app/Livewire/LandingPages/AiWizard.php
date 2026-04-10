<?php

namespace App\Livewire\LandingPages;

use App\Ai\Agents\LandingPageWizardAgent;
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

class AiWizard extends Component
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

            // 2. Create blueprint with one page_builder field
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
                                'fields' => [
                                    [
                                        'label' => 'Page Sections',
                                        'handle' => 'page_sections',
                                        'type' => 'page_builder',
                                        'instructions' => '',
                                        'is_required' => false,
                                        'config' => [],
                                        'sortOrder' => 0,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ]);

            // 3. Link blueprint to collection
            $collection->update(['blueprint_id' => $blueprint->id]);

            // 4. Get the page_builder field to pass its ID to CreateEntry
            $pageBuilderField = $blueprint->tabs->first()
                ->sections->first()
                ->fields->first();

            // 5. Create entry with page builder sections from proposal
            $entry = resolve(CreateEntry::class)->handle([
                'title' => $this->name,
                'slug' => $this->slug,
                'blueprint_id' => $blueprint->id,
                'status' => 'draft',
                'fieldsValues' => [
                    [
                        'field_id' => $pageBuilderField->id,
                        'handle' => 'page_sections',
                        'type' => 'page_builder',
                        'value' => $this->proposal,
                        'children' => [],
                    ],
                ],
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
