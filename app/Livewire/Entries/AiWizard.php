<?php

namespace App\Livewire\Entries;

use App\Ai\Agents\EntryWizardAgent;
use App\Livewire\Actions\CreateEntry;
use App\Models\Blueprint;
use App\Models\Collection;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

class AiWizard extends Component
{
    public string $step = 'describe';

    public ?int $collectionId = null;

    public string $description = '';

    public string $generatedTitle = '';

    public string $generatedSlug = '';

    /** @var array<string, mixed> */
    public array $generatedFields = [];

    /** @var array<int, array{id: int, type: string, label: string, handle: string, config: array}> */
    public array $blueprintFields = [];

    public function updatedCollectionId(): void
    {
        $this->blueprintFields = [];

        if (! $this->collectionId) {
            return;
        }

        $collection = Collection::query()->find($this->collectionId);

        if (! $collection?->blueprint_id) {
            return;
        }

        $blueprint = Blueprint::query()
            ->with('tabs.sections.fields')
            ->find($collection->blueprint_id);

        if (! $blueprint) {
            return;
        }

        $this->blueprintFields = $blueprint->tabs
            ->sortBy('sort_order')
            ->flatMap(fn ($tab) => $tab->sections
                ->sortBy('sort_order')
                ->flatMap(fn ($section) => $section->fields->sortBy('order')))
            ->reject(fn ($field) => $field->type === 'page_builder')
            ->map(fn ($field) => [
                'id' => $field->id,
                'type' => $field->type,
                'label' => $field->label,
                'handle' => $field->handle,
                'config' => $field->config ?? [],
            ])
            ->values()
            ->all();
    }

    public function generate(): void
    {
        $this->validate([
            'collectionId' => 'required|exists:collections,id',
            'description' => 'required|string|min:10|max:2000',
        ]);

        $schemaContext = collect($this->blueprintFields)->map(fn ($f) => [
            'handle' => $f['handle'],
            'type' => $f['type'],
            'label' => $f['label'],
            'options' => $f['config']['options'] ?? [],
        ])->toJson();

        $prompt = "Blueprint fields:\n{$schemaContext}\n\nTopic brief:\n{$this->description}";

        $response = EntryWizardAgent::make()->prompt($prompt);

        $this->generatedTitle = $response['title'] ?? $this->description;
        $this->generatedSlug = $response['slug'] ?? Str::slug($this->generatedTitle);
        $this->generatedFields = $response['fields'] ?? [];

        $this->step = 'review';
    }

    public function save(): void
    {
        $this->validate([
            'generatedTitle' => 'required|string|max:255',
            'generatedSlug' => 'required|string|max:255',
            'collectionId' => 'required|exists:collections,id',
        ]);

        $collection = Collection::query()->findOrFail($this->collectionId);

        $fieldsValues = [];

        foreach ($this->blueprintFields as $field) {
            $value = $this->generatedFields[$field['handle']] ?? null;

            if ($value === null && in_array($field['type'], ['image', 'file', 'page_builder', 'repeater'])) {
                continue;
            }

            $fieldsValues[] = [
                'field_id' => $field['id'],
                'handle' => $field['handle'],
                'type' => $field['type'],
                'value' => $value,
                'children' => [],
            ];
        }

        $entry = app(CreateEntry::class)->handle([
            'blueprint_id' => $collection->blueprint_id,
            'title' => $this->generatedTitle,
            'slug' => $this->generatedSlug,
            'status' => 'draft',
            'fieldsValues' => $fieldsValues,
        ]);

        $this->redirect(route('entries.edit', $entry), navigate: true);
    }

    #[Layout('components.layouts.app')]
    public function render(): View|Factory
    {
        $collections = Collection::query()->where('is_active', true)->get();

        return view('livewire.entries.ai-wizard', [
            'collections' => $collections,
        ]);
    }
}
