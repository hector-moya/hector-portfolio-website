# AI Wizards (Blueprint + Entry) — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add two AI-powered creation wizards — one for blueprints (generates tabs/sections/fields from a description) and one for entries (pre-fills all text fields from a topic brief using the blueprint schema).

**Architecture:** Each wizard is a full-page multi-step Livewire component. AI generation uses dedicated `laravel/ai` agent classes with structured output (`HasStructuredOutput`). The blueprint wizard saves via the existing `CreateBlueprint` action. The entry wizard saves via the existing `CreateEntry` action. All views use Flux UI. Both wizards always redirect to the standard edit page after saving so the user can refine.

**Tech Stack:** Laravel 12, Livewire 4, `laravel/ai` (structured output), Flux UI v2, Pest v4

**Prerequisite:** Plan `2026-04-06-dynamic-frontend-templates.md` does NOT need to be completed first — the two plans are independent.

---

## File Map

**Create:**
- `app/Ai/Agents/BlueprintWizardAgent.php`
- `app/Livewire/Blueprints/AiWizard.php`
- `resources/views/livewire/blueprints/ai-wizard.blade.php`
- `app/Ai/Agents/EntryWizardAgent.php`
- `app/Livewire/Entries/AiWizard.php`
- `resources/views/livewire/entries/ai-wizard.blade.php`
- `tests/Feature/Blueprints/BlueprintAiWizardTest.php`
- `tests/Feature/Entries/EntryAiWizardTest.php`

**Modify:**
- `resources/views/livewire/blueprints/index.blade.php` — add "Create with AI" button
- `resources/views/livewire/entries/index.blade.php` — add "Create with AI" button
- `routes/web.php` — add wizard routes

---

## Task 1: Blueprint Wizard Agent (structured output)

**Files:**
- Create: `app/Ai/Agents/BlueprintWizardAgent.php`

- [ ] **Step 1: Scaffold the agent**

```bash
php artisan make:agent BlueprintWizardAgent --structured --no-interaction
```

This creates `app/Ai/Agents/BlueprintWizardAgent.php`. Replace its contents entirely in the next step.

- [ ] **Step 2: Write the agent**

```php
<?php

namespace App\Ai\Agents;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Attributes\UseSmartestModel;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Promptable;

#[UseSmartestModel]
class BlueprintWizardAgent implements Agent, HasStructuredOutput
{
    use Promptable;

    public function instructions(): string
    {
        return <<<'INSTRUCTIONS'
        You are a CMS blueprint architect. Given a description of a content type, you produce a
        structured blueprint definition with tabs, sections, and fields.

        Rules:
        - Always include a "Content" tab with the main content fields.
        - Always include an "SEO" tab with seo_title (text) and seo_description (textarea) fields.
        - Field handles must be snake_case, unique within the blueprint, and derived from the label.
        - Section handles must be snake_case derived from the section name.
        - Tab handles must be snake_case derived from the tab name.
        - Only use these field types: text, textarea, richtext, number, email, url, date, time,
          toggle, select, radio, image, file, repeater, page_builder.
        - For select and radio fields, include sensible default options in the config.
        - Keep the structure practical: 2–4 tabs, 1–3 sections per tab, 3–8 fields per section.
        - Generate a name and URL-safe slug from the description.
        INSTRUCTIONS;
    }

    /**
     * @return array<string, mixed>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'name'        => $schema->string()->required(),
            'slug'        => $schema->string()->required(),
            'description' => $schema->string()->required(),
            'tabs'        => $schema->array(
                $schema->object([
                    'name'     => $schema->string()->required(),
                    'handle'   => $schema->string()->required(),
                    'sections' => $schema->array(
                        $schema->object([
                            'name'   => $schema->string()->required(),
                            'handle' => $schema->string()->required(),
                            'fields' => $schema->array(
                                $schema->object([
                                    'type'         => $schema->string()->required(),
                                    'label'        => $schema->string()->required(),
                                    'handle'       => $schema->string()->required(),
                                    'instructions' => $schema->string(),
                                    'is_required'  => $schema->boolean()->required(),
                                ])
                            )->required(),
                        ])
                    )->required(),
                ])
            )->required(),
        ];
    }
}
```

- [ ] **Step 3: Commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Ai/Agents/BlueprintWizardAgent.php
git commit -m "feat: add BlueprintWizardAgent with structured output"
```

---

## Task 2: Blueprint Wizard Livewire component + view

**Files:**
- Create: `app/Livewire/Blueprints/AiWizard.php`
- Create: `resources/views/livewire/blueprints/ai-wizard.blade.php`
- Modify: `routes/web.php`
- Modify: `resources/views/livewire/blueprints/index.blade.php`

- [ ] **Step 1: Write the failing tests**

Create `tests/Feature/Blueprints/BlueprintAiWizardTest.php`:

```php
<?php

use App\Ai\Agents\BlueprintWizardAgent;
use App\Livewire\Blueprints\AiWizard;
use App\Models\Blueprint;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    actingAs(User::factory()->create(['role' => 'admin']));
});

test('blueprint ai wizard page loads', function () {
    $this->get(route('blueprints.ai-wizard'))->assertOk();
});

test('generate step calls the AI agent and sets proposal', function () {
    BlueprintWizardAgent::fake([json_encode([
        'name'        => 'Blog Post',
        'slug'        => 'blog-post',
        'description' => 'A blog post blueprint',
        'tabs'        => [
            [
                'name'     => 'Content',
                'handle'   => 'content',
                'sections' => [
                    [
                        'name'   => 'Main',
                        'handle' => 'main',
                        'fields' => [
                            ['type' => 'richtext', 'label' => 'Content', 'handle' => 'content', 'instructions' => '', 'is_required' => false],
                        ],
                    ],
                ],
            ],
        ],
    ])]);

    Livewire::test(AiWizard::class)
        ->set('description', 'A blog post blueprint')
        ->call('generate')
        ->assertSet('step', 'review')
        ->assertSet('proposal.name', 'Blog Post');
});

test('save creates blueprint with tabs sections and fields', function () {
    BlueprintWizardAgent::fake([json_encode([
        'name'        => 'Portfolio Item',
        'slug'        => 'portfolio-item',
        'description' => 'A portfolio item',
        'tabs'        => [
            [
                'name'     => 'Content',
                'handle'   => 'content',
                'sections' => [
                    [
                        'name'   => 'Details',
                        'handle' => 'details',
                        'fields' => [
                            ['type' => 'text', 'label' => 'Title', 'handle' => 'title', 'instructions' => '', 'is_required' => true],
                            ['type' => 'textarea', 'label' => 'Summary', 'handle' => 'summary', 'instructions' => '', 'is_required' => false],
                        ],
                    ],
                ],
            ],
        ],
    ])]);

    Livewire::test(AiWizard::class)
        ->set('description', 'A portfolio item blueprint')
        ->call('generate')
        ->call('save')
        ->assertRedirect(fn ($url) => str_contains($url, '/blueprints/'));

    $blueprint = Blueprint::where('slug', 'portfolio-item')->first();
    expect($blueprint)->not->toBeNull();
    expect($blueprint->tabs)->toHaveCount(1);
    expect($blueprint->tabs->first()->sections)->toHaveCount(1);
    expect($blueprint->tabs->first()->sections->first()->fields)->toHaveCount(2);
});

test('remove tab removes it from proposal', function () {
    Livewire::test(AiWizard::class)
        ->set('step', 'review')
        ->set('proposal', [
            'name' => 'Test', 'slug' => 'test', 'description' => 'test',
            'tabs' => [
                ['name' => 'Tab A', 'handle' => 'tab-a', 'sections' => []],
                ['name' => 'Tab B', 'handle' => 'tab-b', 'sections' => []],
            ],
        ])
        ->call('removeTab', 0)
        ->assertSet('proposal.tabs.0.name', 'Tab B');
});
```

- [ ] **Step 2: Run tests to confirm they fail**

```bash
php artisan test --compact --filter=BlueprintAiWizardTest
```

Expected: FAIL (class not found)

- [ ] **Step 3: Create `AiWizard` Livewire component**

```php
<?php

namespace App\Livewire\Blueprints;

use App\Ai\Agents\BlueprintWizardAgent;
use App\Enums\FieldType;
use App\Livewire\Actions\Blueprints\CreateBlueprint;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

class AiWizard extends Component
{
    public string $step = 'describe';

    public string $description = '';

    /** @var array<string, mixed> */
    public array $proposal = [];

    public bool $loading = false;

    public function generate(): void
    {
        $this->validate(['description' => 'required|string|min:10|max:1000']);

        $this->loading = true;

        $response = BlueprintWizardAgent::make()->prompt($this->description);

        $this->proposal = [
            'name'        => $response['name'],
            'slug'        => $response['slug'],
            'description' => $response['description'],
            'tabs'        => $response['tabs'],
        ];

        $this->loading = false;
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
                        'label'        => $field['label'],
                        'handle'       => $field['handle'],
                        'type'         => $type->value,
                        'instructions' => $field['instructions'] ?? '',
                        'is_required'  => $field['is_required'] ?? false,
                        'config'       => $type->defaultConfig(),
                        'sortOrder'    => $fieldIndex,
                    ];
                }

                $sections[] = [
                    'name'         => $section['name'],
                    'handle'       => $section['handle'] ?? Str::slug($section['name']),
                    'instructions' => '',
                    'fields'       => $fields,
                    'sortOrder'    => $sectionIndex,
                ];
            }

            $tabs[] = [
                'name'      => $tab['name'],
                'handle'    => $tab['handle'] ?? Str::slug($tab['name']),
                'sections'  => $sections,
                'sortOrder' => $tabIndex,
            ];
        }

        $blueprint = app(CreateBlueprint::class)->create([
            'name'        => $this->proposal['name'],
            'slug'        => $this->proposal['slug'],
            'description' => $this->proposal['description'],
            'is_active'   => false,
            'tabs'        => $tabs,
        ]);

        $this->redirect(route('blueprints.edit', $blueprint), navigate: true);
    }

    #[Layout('components.layouts.app')]
    public function render(): View|Factory
    {
        return view('livewire.blueprints.ai-wizard');
    }
}
```

- [ ] **Step 4: Create the view**

```blade
{{-- resources/views/livewire/blueprints/ai-wizard.blade.php --}}
<div class="mx-auto max-w-4xl flex flex-col gap-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('Blueprint AI Wizard') }}</flux:heading>
            <flux:text>{{ __('Describe your content type and let AI generate the structure.') }}</flux:text>
        </div>
        <flux:button icon="arrow-uturn-left" wire:navigate href="{{ route('blueprints.index') }}">
            {{ __('Cancel') }}
        </flux:button>
    </div>

    {{-- Step indicator --}}
    <div class="flex gap-2">
        <flux:badge :variant="$step === 'describe' ? 'primary' : 'zinc'">1 Describe</flux:badge>
        <flux:badge :variant="$step === 'review' ? 'primary' : 'zinc'">2 Review</flux:badge>
    </div>

    {{-- Step 1: Describe --}}
    @if($step === 'describe')
        <flux:card class="space-y-4">
            <flux:heading size="lg">{{ __('What is this blueprint for?') }}</flux:heading>
            <flux:textarea
                wire:model="description"
                rows="4"
                placeholder="A blog post blueprint with featured image, rich text content, author bio, tags, and SEO fields."
                description="{{ __('Be as specific as you like — the more detail, the better the suggestion.') }}"
            />
        </flux:card>

        <div class="flex justify-end">
            <flux:button
                variant="primary"
                wire:click="generate"
                wire:loading.attr="disabled"
                icon="sparkles"
            >
                <span wire:loading.remove wire:target="generate">{{ __('Generate Blueprint') }}</span>
                <span wire:loading wire:target="generate">{{ __('Generating…') }}</span>
            </flux:button>
        </div>
    @endif

    {{-- Step 2: Review --}}
    @if($step === 'review')
        <flux:callout variant="info" icon="information-circle">
            {{ __('Review the proposed structure. You can remove items — all editing is available after save.') }}
        </flux:callout>

        <flux:card class="p-0! divide-y divide-zinc-200 dark:divide-zinc-700">
            <div class="px-6 py-4">
                <flux:heading size="lg">{{ $proposal['name'] }}</flux:heading>
                <flux:text class="text-sm">{{ $proposal['description'] }}</flux:text>
            </div>

            @foreach($proposal['tabs'] as $tabIndex => $tab)
                <div class="px-6 py-4">
                    <div class="flex items-center justify-between">
                        <flux:badge>Tab: {{ $tab['name'] }}</flux:badge>
                        <flux:button size="sm" variant="ghost" icon="trash" wire:click="removeTab({{ $tabIndex }})"/>
                    </div>

                    @foreach($tab['sections'] as $sectionIndex => $section)
                        <div class="mt-3 ml-4 space-y-2">
                            <div class="flex items-center justify-between">
                                <flux:text class="text-sm font-semibold">Section: {{ $section['name'] }}</flux:text>
                                <flux:button size="sm" variant="ghost" icon="trash" wire:click="removeSection({{ $tabIndex }}, {{ $sectionIndex }})"/>
                            </div>

                            <div class="ml-4 space-y-1">
                                @foreach($section['fields'] as $fieldIndex => $field)
                                    <div class="flex items-center justify-between py-1">
                                        <div class="flex items-center gap-2">
                                            <flux:badge size="sm" variant="zinc">{{ $field['type'] }}</flux:badge>
                                            <flux:text class="text-sm">{{ $field['label'] }}</flux:text>
                                            <flux:text class="text-xs text-zinc-400">{{ $field['handle'] }}</flux:text>
                                            @if(!empty($field['is_required']))
                                                <flux:badge size="sm" variant="primary">required</flux:badge>
                                            @endif
                                        </div>
                                        <flux:button size="sm" variant="ghost" icon="x-mark" wire:click="removeField({{ $tabIndex }}, {{ $sectionIndex }}, {{ $fieldIndex }})"/>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </flux:card>

        <div class="flex items-center justify-between">
            <flux:button variant="ghost" wire:click="$set('step', 'describe')">
                {{ __('← Back') }}
            </flux:button>
            <flux:button variant="primary" wire:click="save" icon="check">
                {{ __('Create Blueprint') }}
            </flux:button>
        </div>
    @endif
</div>
```

- [ ] **Step 5: Add route to `routes/web.php`**

Inside the `Route::middleware(['auth'])` group, after the existing blueprints routes:

```php
Route::get('blueprints/ai-wizard', \App\Livewire\Blueprints\AiWizard::class)->name('blueprints.ai-wizard');
```

**Important:** Place this BEFORE `Route::get('blueprints/{blueprint}/create', ...)` so `ai-wizard` is not matched as a `{blueprint}` parameter.

- [ ] **Step 6: Add "Create with AI" button to blueprints index**

Open `resources/views/livewire/blueprints/index.blade.php`. Find the "Create Blueprint" button and add a second button alongside it:

```blade
<flux:button icon="sparkles" wire:navigate href="{{ route('blueprints.ai-wizard') }}" variant="ghost">
    {{ __('Create with AI') }}
</flux:button>
```

- [ ] **Step 7: Run the tests**

```bash
php artisan test --compact --filter=BlueprintAiWizardTest
```

Expected: PASS (4 tests)

- [ ] **Step 8: Format and commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Livewire/Blueprints/AiWizard.php \
  resources/views/livewire/blueprints/ai-wizard.blade.php \
  resources/views/livewire/blueprints/index.blade.php \
  routes/web.php \
  tests/Feature/Blueprints/BlueprintAiWizardTest.php
git commit -m "feat: add Blueprint AI Wizard Livewire component"
```

---

## Task 3: Entry Wizard Agent (structured output)

**Files:**
- Create: `app/Ai/Agents/EntryWizardAgent.php`

The entry wizard agent receives the full blueprint schema as context in its prompt, then generates field values. The prompt is built by the Livewire component and passed to `->prompt()`.

- [ ] **Step 1: Scaffold the agent**

```bash
php artisan make:agent EntryWizardAgent --structured --no-interaction
```

- [ ] **Step 2: Write the agent**

The entry wizard agent uses a flexible schema: it returns a map of `handle → value` pairs. Since handles vary per blueprint, we use a dynamic schema approach via a JSON string output that we parse ourselves. The agent returns a JSON object and we access it by handle.

```php
<?php

namespace App\Ai\Agents;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Attributes\UseSmartestModel;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Promptable;

#[UseSmartestModel]
class EntryWizardAgent implements Agent, HasStructuredOutput
{
    use Promptable;

    public function instructions(): string
    {
        return <<<'INSTRUCTIONS'
        You are a CMS content writer. Given a blueprint schema (list of fields with their types and handles)
        and a topic brief, you generate appropriate content values for each text-based field.

        Rules:
        - Fill text, textarea, email, url, number, date fields with real, relevant content.
        - For richtext fields, generate full HTML content (use <p>, <h2>, <ul>, <strong>, <em> tags).
        - For select and radio fields, pick the best option from the provided options array.
        - For toggle fields, return true or false as a boolean.
        - For image and file fields, return null.
        - For repeater fields, return null (scaffold handled separately).
        - For page_builder fields, return null.
        - For date fields, return today's date in Y-m-d format.
        - For seo_title: generate a concise, keyword-rich title under 60 characters.
        - For seo_description: generate a compelling meta description under 160 characters.
        - Return a flat JSON object where each key is the field handle and the value is the generated content.
        INSTRUCTIONS;
    }

    /**
     * @return array<string, mixed>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'title'  => $schema->string()->required(),
            'slug'   => $schema->string()->required(),
            'fields' => $schema->object([])->additionalProperties($schema->string()->nullable()),
        ];
    }
}
```

- [ ] **Step 3: Commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Ai/Agents/EntryWizardAgent.php
git commit -m "feat: add EntryWizardAgent with structured output"
```

---

## Task 4: Entry Wizard Livewire component + view

**Files:**
- Create: `app/Livewire/Entries/AiWizard.php`
- Create: `resources/views/livewire/entries/ai-wizard.blade.php`
- Modify: `routes/web.php`
- Modify: `resources/views/livewire/entries/index.blade.php`

- [ ] **Step 1: Write the failing tests**

Create `tests/Feature/Entries/EntryAiWizardTest.php`:

```php
<?php

use App\Ai\Agents\EntryWizardAgent;
use App\Enums\FieldType;
use App\Livewire\Entries\AiWizard;
use App\Models\Blueprint;
use App\Models\Collection;
use App\Models\Entry;
use App\Models\Field;
use App\Models\Section;
use App\Models\Tab;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    actingAs(User::factory()->create(['role' => 'admin']));
});

test('entry ai wizard page loads', function () {
    $this->get(route('entries.ai-wizard'))->assertOk();
});

test('selecting a collection loads blueprint fields', function () {
    $blueprint = Blueprint::factory()->create();
    $collection = Collection::factory()->create(['blueprint_id' => $blueprint->id]);

    $tab = $blueprint->tabs()->create(['name' => 'Content', 'handle' => 'content', 'sort_order' => 0]);
    $section = $tab->sections()->create(['name' => 'Main', 'handle' => 'main', 'blueprint_id' => $blueprint->id, 'instructions' => '', 'sort_order' => 0]);
    Field::factory()->create(['blueprint_id' => $blueprint->id, 'section_id' => $section->id, 'type' => 'text', 'label' => 'Title', 'handle' => 'title']);

    Livewire::test(AiWizard::class)
        ->set('collectionId', $collection->id)
        ->assertSet('step', 'describe')
        ->assertNotEmpty('blueprintFields');
});

test('generate step calls the AI agent and sets generatedFields', function () {
    EntryWizardAgent::fake([json_encode([
        'title'  => 'My Generated Post',
        'slug'   => 'my-generated-post',
        'fields' => ['excerpt' => 'A great post about stuff.', 'content' => '<p>Full content here.</p>'],
    ])]);

    $blueprint = Blueprint::factory()->create();
    $collection = Collection::factory()->create(['blueprint_id' => $blueprint->id]);

    Livewire::test(AiWizard::class)
        ->set('collectionId', $collection->id)
        ->set('description', 'A post about Laravel testing best practices')
        ->call('generate')
        ->assertSet('step', 'review')
        ->assertSet('generatedTitle', 'My Generated Post');
});

test('save creates entry with generated field values as draft', function () {
    $blueprint = Blueprint::factory()->create();
    $collection = Collection::factory()->create(['blueprint_id' => $blueprint->id]);

    $tab = Tab::factory()->create(['blueprint_id' => $blueprint->id, 'name' => 'Content', 'handle' => 'content']);
    $section = Section::factory()->create(['blueprint_id' => $blueprint->id, 'tab_id' => $tab->id, 'name' => 'Main', 'handle' => 'main']);
    $field = Field::factory()->create([
        'blueprint_id' => $blueprint->id,
        'section_id'   => $section->id,
        'type'         => FieldType::Textarea->value,
        'label'        => 'Excerpt',
        'handle'       => 'excerpt',
    ]);

    EntryWizardAgent::fake([json_encode([
        'title'  => 'Test Entry',
        'slug'   => 'test-entry',
        'fields' => ['excerpt' => 'This is the excerpt.'],
    ])]);

    Livewire::test(AiWizard::class)
        ->set('collectionId', $collection->id)
        ->set('description', 'A test entry')
        ->call('generate')
        ->call('save')
        ->assertRedirect(fn ($url) => str_contains($url, '/entries/'));

    $entry = Entry::where('slug', 'test-entry')->first();
    expect($entry)->not->toBeNull();
    expect($entry->status)->toBe('draft');
    expect($entry->elements->firstWhere('handle', 'excerpt')?->value)->toBe('This is the excerpt.');
});
```

- [ ] **Step 2: Run tests to confirm they fail**

```bash
php artisan test --compact --filter=EntryAiWizardTest
```

Expected: FAIL (class not found)

- [ ] **Step 3: Create `AiWizard` Livewire component for entries**

```php
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
use Livewire\Attributes\Computed;
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

    public bool $loading = false;

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
                'id'     => $field->id,
                'type'   => $field->type,
                'label'  => $field->label,
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
            'description'  => 'required|string|min:10|max:2000',
        ]);

        $this->loading = true;

        $schemaContext = collect($this->blueprintFields)->map(fn ($f) => [
            'handle'  => $f['handle'],
            'type'    => $f['type'],
            'label'   => $f['label'],
            'options' => $f['config']['options'] ?? [],
        ])->toJson();

        $prompt = "Blueprint fields:\n{$schemaContext}\n\nTopic brief:\n{$this->description}";

        $response = EntryWizardAgent::make()->prompt($prompt);

        $this->generatedTitle  = $response['title'] ?? $this->description;
        $this->generatedSlug   = $response['slug'] ?? Str::slug($this->generatedTitle);
        $this->generatedFields = $response['fields'] ?? [];

        $this->loading = false;
        $this->step = 'review';
    }

    public function save(): void
    {
        $this->validate([
            'generatedTitle' => 'required|string|max:255',
            'generatedSlug'  => 'required|string|max:255',
            'collectionId'   => 'required|exists:collections,id',
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
                'handle'   => $field['handle'],
                'type'     => $field['type'],
                'value'    => $value,
                'children' => [],
            ];
        }

        $entry = app(CreateEntry::class)->handle([
            'blueprint_id'    => $collection->blueprint_id,
            'title'           => $this->generatedTitle,
            'slug'            => $this->generatedSlug,
            'status'          => 'draft',
            'fieldsValues'    => $fieldsValues,
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
```

- [ ] **Step 4: Create the view**

```blade
{{-- resources/views/livewire/entries/ai-wizard.blade.php --}}
<div class="mx-auto max-w-4xl flex flex-col gap-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('Entry AI Wizard') }}</flux:heading>
            <flux:text>{{ __('Describe your entry and let AI pre-fill the content.') }}</flux:text>
        </div>
        <flux:button icon="arrow-uturn-left" wire:navigate href="{{ route('entries') }}">
            {{ __('Cancel') }}
        </flux:button>
    </div>

    {{-- Step indicator --}}
    <div class="flex gap-2">
        <flux:badge :variant="$step === 'describe' ? 'primary' : 'zinc'">1 Describe</flux:badge>
        <flux:badge :variant="$step === 'review' ? 'primary' : 'zinc'">2 Review & Save</flux:badge>
    </div>

    {{-- Step 1: Describe --}}
    @if($step === 'describe')
        <flux:card class="space-y-4">
            <flux:heading size="lg">{{ __('What are you creating?') }}</flux:heading>

            <flux:select
                label="{{ __('Collection') }}"
                wire:model.live="collectionId"
                description="{{ __('The collection this entry belongs to.') }}"
            >
                <flux:select.option value="">{{ __('Select a collection') }}</flux:select.option>
                @foreach($collections as $collection)
                    <flux:select.option value="{{ $collection->id }}">{{ $collection->name }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:textarea
                wire:model="description"
                label="{{ __('Topic Brief') }}"
                rows="4"
                placeholder="A blog post about setting up Laravel with Docker for local development, covering installation, configuration, and common gotchas."
                description="{{ __('Describe the entry topic. The AI will use the blueprint fields to generate matching content.') }}"
            />
        </flux:card>

        <div class="flex justify-end">
            <flux:button
                variant="primary"
                wire:click="generate"
                wire:loading.attr="disabled"
                :disabled="!$collectionId"
                icon="sparkles"
            >
                <span wire:loading.remove wire:target="generate">{{ __('Generate Content') }}</span>
                <span wire:loading wire:target="generate">{{ __('Generating…') }}</span>
            </flux:button>
        </div>
    @endif

    {{-- Step 2: Review --}}
    @if($step === 'review')
        <flux:callout variant="info" icon="information-circle">
            {{ __('Review and edit the generated content before saving. The entry will be saved as a draft.') }}
        </flux:callout>

        <flux:card class="space-y-6">
            <flux:input
                label="{{ __('Title') }}"
                wire:model="generatedTitle"
                badge="{{ __('Required') }}"
            />
            <flux:input
                label="{{ __('Slug') }}"
                wire:model="generatedSlug"
                description="{{ __('URL-friendly identifier') }}"
            />

            <flux:separator />

            @foreach($blueprintFields as $field)
                @php $value = $generatedFields[$field['handle']] ?? null; @endphp

                @if(in_array($field['type'], ['image', 'file', 'page_builder', 'repeater']))
                    <div>
                        <flux:text class="text-sm font-medium">{{ $field['label'] }}</flux:text>
                        <flux:badge variant="zinc" class="mt-1">{{ __('Upload/configure after saving') }}</flux:badge>
                    </div>
                @elseif($field['type'] === 'richtext')
                    <div>
                        <flux:label>{{ $field['label'] }}</flux:label>
                        <flux:textarea wire:model="generatedFields.{{ $field['handle'] }}" rows="8" />
                    </div>
                @elseif($field['type'] === 'textarea')
                    <flux:textarea
                        label="{{ $field['label'] }}"
                        wire:model="generatedFields.{{ $field['handle'] }}"
                        rows="4"
                    />
                @elseif($field['type'] === 'toggle')
                    <flux:switch
                        label="{{ $field['label'] }}"
                        wire:model="generatedFields.{{ $field['handle'] }}"
                    />
                @elseif($field['type'] === 'select')
                    <flux:select label="{{ $field['label'] }}" wire:model="generatedFields.{{ $field['handle'] }}">
                        @foreach($field['config']['options'] ?? [] as $option)
                            <flux:select.option value="{{ $option['value'] }}">{{ $option['label'] }}</flux:select.option>
                        @endforeach
                    </flux:select>
                @else
                    <flux:input
                        label="{{ $field['label'] }}"
                        wire:model="generatedFields.{{ $field['handle'] }}"
                    />
                @endif
            @endforeach
        </flux:card>

        <div class="flex items-center justify-between">
            <flux:button variant="ghost" wire:click="$set('step', 'describe')">
                {{ __('← Back') }}
            </flux:button>
            <flux:button
                variant="primary"
                wire:click="save"
                icon="check"
            >
                {{ __('Save as Draft') }}
            </flux:button>
        </div>
    @endif
</div>
```

- [ ] **Step 5: Add route to `routes/web.php`**

Inside the `Route::middleware(['auth'])` group, after the existing entries routes:

```php
Route::get('entries/ai-wizard', \App\Livewire\Entries\AiWizard::class)->name('entries.ai-wizard');
```

**Important:** Place this BEFORE `Route::get('entries/{entry}/edit', ...)` so `ai-wizard` is not matched as `{entry}`.

- [ ] **Step 6: Add "Create with AI" button to entries index**

Open `resources/views/livewire/entries/index.blade.php`. Find the "Create Entry" button and add alongside it:

```blade
<flux:button icon="sparkles" wire:navigate href="{{ route('entries.ai-wizard') }}" variant="ghost">
    {{ __('Create with AI') }}
</flux:button>
```

- [ ] **Step 7: Run the tests**

```bash
php artisan test --compact --filter=EntryAiWizardTest
```

Expected: PASS (4 tests)

- [ ] **Step 8: Format and commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Livewire/Entries/AiWizard.php \
  resources/views/livewire/entries/ai-wizard.blade.php \
  resources/views/livewire/entries/index.blade.php \
  routes/web.php \
  tests/Feature/Entries/EntryAiWizardTest.php
git commit -m "feat: add Entry AI Wizard Livewire component"
```

---

## Task 5: Run full test suite and verify

- [ ] **Step 1: Run all tests**

```bash
php artisan test --compact
```

Expected: All tests pass.

- [ ] **Step 2: Run pint on the whole project**

```bash
vendor/bin/pint --format agent
```

Expected: No changes (everything formatted already).

- [ ] **Step 3: Verify blueprint wizard in browser**

1. Log in to the admin.
2. Go to `/blueprints`.
3. Click "Create with AI".
4. Enter: *"A portfolio project blueprint with project title, description, technologies used (repeater), live URL, GitHub URL, featured image, and an SEO tab."*
5. Click "Generate Blueprint".
6. Confirm the review tree shows expected tabs and fields.
7. Click "Create Blueprint".
8. Confirm redirect to the standard blueprint edit page with all tabs/sections/fields present.

- [ ] **Step 4: Verify entry wizard in browser**

1. Go to `/entries`.
2. Click "Create with AI".
3. Select a collection that has an active blueprint.
4. Enter a topic brief.
5. Click "Generate Content".
6. Confirm review step shows populated fields.
7. Click "Save as Draft".
8. Confirm redirect to standard entry edit page with all text fields pre-filled and status = draft.
