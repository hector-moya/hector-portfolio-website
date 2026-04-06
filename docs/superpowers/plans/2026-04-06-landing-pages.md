# Landing Pages Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add single-type collection support so landing pages render page builder sections at `/{slug}`, bring `Home.php` into the same pipeline, and add an AI wizard that creates a full landing page (collection + blueprint + entry) from a description.

**Architecture:** `CollectionIndex` detects `settings.type === 'single'` and renders the first published entry through a new `landing-page` detail template, which loops over `getPageBuilderSections()` using the existing six `<x-sections.*>` components. `Home.php` is simplified to use the same `resolveAssets()` pattern. A new `LandingPages\AiWizard` component creates all three models in one `save()` call.

**Tech Stack:** Laravel 12, Livewire 4, Flux UI v2, Tailwind v4, Pest v4, `laravel/ai` (structured output)

---

## File Map

**Create:**
- `resources/views/components/templates/detail/landing-page.blade.php` — renders page builder sections via `<x-sections.*>`
- `app/Ai/Agents/LandingPageWizardAgent.php` — structured output agent; produces ordered sections array
- `app/Livewire/LandingPages/AiWizard.php` — 2-step wizard: describe → review → save
- `resources/views/livewire/landing-pages/ai-wizard.blade.php` — Flux UI wizard view
- `tests/Feature/Frontend/LandingPageTest.php` — single-type collection rendering tests
- `tests/Feature/LandingPages/LandingPageAiWizardTest.php` — wizard tests

**Modify:**
- `app/Support/TemplateLayouts.php` — add `'landing-page'` to `detailTemplates()`
- `app/Livewire/Forms/CollectionForm.php` — add `$type` property stored in `settings['type']`
- `resources/views/livewire/collections/create.blade.php` — add Collection Type dropdown
- `resources/views/livewire/collections/edit.blade.php` — add Collection Type dropdown
- `app/Livewire/Frontend/CollectionIndex.php` — single-type branch + `resolveAssets()` private method
- `resources/views/livewire/frontend/collection-index.blade.php` — `$isSingle` branch
- `app/Livewire/Frontend/Home.php` — rename `loadLayoutAssets` → `resolveAssets`, remove `$entry` param
- `resources/views/livewire/frontend/home.blade.php` — remove hardcoded legacy field fallback block
- `resources/views/livewire/collections/index.blade.php` — add "Create with AI" button
- `routes/web.php` — add `landing-pages/ai-wizard` route
- `tests/Feature/Frontend/HomeSectionsTest.php` — remove legacy fallback test

---

## Task 1: TemplateLayouts + CollectionForm type field

**Files:**
- Modify: `app/Support/TemplateLayouts.php`
- Modify: `app/Livewire/Forms/CollectionForm.php`
- Modify: `resources/views/livewire/collections/create.blade.php`
- Modify: `resources/views/livewire/collections/edit.blade.php`

- [ ] **Step 1: Write the failing tests**

```bash
php artisan make:test --pest CollectionTypeTest
```

Edit `tests/Feature/CollectionTypeTest.php`:

```php
<?php

use App\Livewire\Collections\Create;
use App\Livewire\Collections\Edit;
use App\Models\Collection;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    actingAs(User::factory()->create(['role' => 'admin']));
});

test('collection type defaults to standard', function () {
    Livewire::test(Create::class)
        ->assertSet('form.type', 'standard');
});

test('create collection stores type in settings', function () {
    Livewire::test(Create::class)
        ->set('form.name', 'About Page')
        ->set('form.type', 'single')
        ->call('save');

    $collection = Collection::where('slug', 'about-page')->first();
    expect($collection->settings['type'])->toBe('single');
});

test('edit collection loads type from settings', function () {
    $collection = Collection::factory()->create([
        'settings' => ['type' => 'single'],
    ]);

    Livewire::test(Edit::class, ['collection' => $collection])
        ->assertSet('form.type', 'single');
});

test('update collection persists type change', function () {
    $collection = Collection::factory()->create([
        'settings' => ['type' => 'standard'],
    ]);

    Livewire::test(Edit::class, ['collection' => $collection])
        ->set('form.type', 'single')
        ->call('save');

    expect($collection->fresh()->settings['type'])->toBe('single');
});
```

- [ ] **Step 2: Run tests to confirm they fail**

```bash
php artisan test --compact --filter=CollectionTypeTest
```

Expected: 4 failures — `form.type` property does not exist.

- [ ] **Step 3: Add `landing-page` to TemplateLayouts**

In `app/Support/TemplateLayouts.php`, update `detailTemplates()`:

```php
public static function detailTemplates(): array
{
    return [
        'article'      => 'Article',
        'full-width'   => 'Full Width',
        'minimal'      => 'Minimal',
        'landing-page' => 'Landing Page',
    ];
}
```

- [ ] **Step 4: Add `$type` to CollectionForm**

In `app/Livewire/Forms/CollectionForm.php`, add after `$index_template`:

```php
#[Validate('nullable|string|in:standard,single')]
public string $type = 'standard';
```

In `setCollection()`, add:

```php
$this->type = $collection->settings['type'] ?? 'standard';
```

In `create()`, update the `settings` array:

```php
'settings' => array_filter([
    'theme'          => $this->theme ?: null,
    'index_template' => $this->index_template ?: null,
    'type'           => $this->type !== 'standard' ? $this->type : null,
]),
```

In `update()`, update the `settings` merge:

```php
'settings' => array_merge($collection->settings ?? [], [
    'theme'          => $this->theme ?: null,
    'index_template' => $this->index_template ?: null,
    'type'           => $this->type ?: 'standard',
]),
```

Also add `'type'` to the `reset()` call in `create()`:

```php
$this->reset('name', 'slug', 'description', 'blueprint_id', 'is_active', 'theme', 'index_template', 'type');
```

- [ ] **Step 5: Add Collection Type dropdown to create view**

In `resources/views/livewire/collections/create.blade.php`, add after the `{{-- Index Template --}}` block and before `{{-- Status --}}`:

```blade
{{-- Collection Type --}}
<flux:select
    label="{{ __('Collection Type') }}"
    wire:model.live="form.type"
    description="{{ __('Standard shows an entry listing. Single Page renders one entry directly (for landing pages).') }}"
>
    <flux:select.option value="standard">{{ __('Standard') }}</flux:select.option>
    <flux:select.option value="single">{{ __('Single Page') }}</flux:select.option>
</flux:select>

{{-- Index Template — hidden for single-type --}}
@if($form->type !== 'single')
```

Then wrap the existing Index Template block's closing tag:

```blade
@endif
```

So the full replace around the Index Template block becomes:

```blade
{{-- Collection Type --}}
<flux:select
    label="{{ __('Collection Type') }}"
    wire:model.live="form.type"
    description="{{ __('Standard shows an entry listing. Single Page renders one entry directly (for landing pages).') }}"
>
    <flux:select.option value="standard">{{ __('Standard') }}</flux:select.option>
    <flux:select.option value="single">{{ __('Single Page') }}</flux:select.option>
</flux:select>

{{-- Index Template --}}
@if($form->type !== 'single')
<flux:select label="{{ __('Index Template') }}" wire:model="form.index_template" description="{{ __('Layout used for the collection listing page') }}">
    <flux:select.option value="">{{ __('Default (Card Grid)') }}</flux:select.option>
    @foreach (\App\Support\TemplateLayouts::indexTemplates() as $value => $label)
        <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
    @endforeach
</flux:select>
@endif
```

- [ ] **Step 6: Add Collection Type dropdown to edit view**

Apply the same change to `resources/views/livewire/collections/edit.blade.php` — identical replacement of the Index Template block, adding the Collection Type select above it with the `@if` guard.

- [ ] **Step 7: Run tests to confirm they pass**

```bash
php artisan test --compact --filter=CollectionTypeTest
```

Expected: 4 passed.

- [ ] **Step 8: Run pint**

```bash
vendor/bin/pint --dirty --format agent
```

- [ ] **Step 9: Commit**

```bash
git add app/Support/TemplateLayouts.php app/Livewire/Forms/CollectionForm.php \
        resources/views/livewire/collections/create.blade.php \
        resources/views/livewire/collections/edit.blade.php \
        tests/Feature/CollectionTypeTest.php
git commit -m "feat: add collection type field (standard/single) and landing-page to TemplateLayouts"
```

---

## Task 2: Landing page template + CollectionIndex single-type branch

**Files:**
- Create: `resources/views/components/templates/detail/landing-page.blade.php`
- Modify: `app/Livewire/Frontend/CollectionIndex.php`
- Modify: `resources/views/livewire/frontend/collection-index.blade.php`
- Create: `tests/Feature/Frontend/LandingPageTest.php`

- [ ] **Step 1: Write the failing tests**

```bash
php artisan make:test --pest Frontend/LandingPageTest
```

Edit `tests/Feature/Frontend/LandingPageTest.php`:

```php
<?php

use App\Models\Blueprint;
use App\Models\Collection;
use App\Models\Entry;
use App\Models\Field;

test('single-type collection renders entry directly at collection url', function () {
    $blueprint = Blueprint::factory()->create();
    $collection = Collection::factory()->create([
        'slug'        => 'about',
        'blueprint_id' => $blueprint->id,
        'is_active'   => true,
        'settings'    => ['type' => 'single'],
    ]);

    Entry::factory()->create([
        'blueprint_id' => $blueprint->id,
        'title'        => 'About Us',
        'slug'         => 'about',
        'status'       => 'published',
        'published_at' => now(),
        'layout'       => [
            [
                '_id'  => 'sec-1',
                'type' => 'hero',
                'data' => [
                    'title'               => 'We Build Things',
                    'subtitle'            => '',
                    'content'             => '',
                    'bg_image'            => null,
                    'cta_text'            => '',
                    'cta_url'             => '',
                    'secondary_cta_text'  => '',
                    'secondary_cta_url'   => '',
                ],
            ],
        ],
    ]);

    $this->get('/about')
        ->assertOk()
        ->assertSee('We Build Things');
});

test('single-type collection returns 404 for inactive collection', function () {
    $blueprint = Blueprint::factory()->create();
    Collection::factory()->create([
        'slug'      => 'inactive-page',
        'is_active' => false,
        'settings'  => ['type' => 'single'],
    ]);

    $this->get('/inactive-page')->assertNotFound();
});

test('single-type collection with no published entry shows empty state', function () {
    $blueprint = Blueprint::factory()->create();
    Collection::factory()->create([
        'slug'        => 'empty-page',
        'blueprint_id' => $blueprint->id,
        'is_active'   => true,
        'settings'    => ['type' => 'single'],
    ]);

    // No entries created — should not 500, should render view
    $this->get('/empty-page')->assertOk();
});

test('single-type does not show entry listing', function () {
    $blueprint = Blueprint::factory()->create();
    $collection = Collection::factory()->create([
        'slug'        => 'services',
        'blueprint_id' => $blueprint->id,
        'is_active'   => true,
        'settings'    => ['type' => 'single'],
    ]);

    Entry::factory()->count(3)->create([
        'blueprint_id' => $blueprint->id,
        'status'       => 'published',
        'published_at' => now(),
        'layout'       => [],
    ]);

    // Standard listing would show three entries; single-type shows first one only
    // Verify no pagination is rendered (single page has no page nav)
    $response = $this->get('/services')->assertOk();
    // The landing-page template wraps in x-themes.wrapper, not the index card-grid
    $response->assertDontSee('card-grid');
});
```

- [ ] **Step 2: Run tests to confirm they fail**

```bash
php artisan test --compact --filter=LandingPageTest
```

Expected: failures — single-type renders listing instead of landing-page template.

- [ ] **Step 3: Create the landing-page detail template**

Create `resources/views/components/templates/detail/landing-page.blade.php`:

```blade
@props(['entry', 'sections' => [], 'assets' => null, 'theme' => 'greenpeace'])

@php
    $assets ??= collect();
@endphp

<x-themes.wrapper :theme="$theme">
    @forelse($sections as $section)
        <x-dynamic-component
            :component="'sections.' . str_replace('_', '-', $section['type'])"
            :section="$section"
            :assets="$assets"
            wire:key="section-{{ $section['_id'] }}"
        />
    @empty
        <div class="mx-auto max-w-3xl py-24 text-center">
            @if($entry)
                <flux:heading size="xl">{{ $entry->title }}</flux:heading>
            @endif
            <flux:text class="mt-4">{{ __('Content coming soon.') }}</flux:text>
        </div>
    @endforelse
</x-themes.wrapper>
```

- [ ] **Step 4: Add single-type branch to CollectionIndex**

Replace the full contents of `app/Livewire/Frontend/CollectionIndex.php` with:

```php
<?php

namespace App\Livewire\Frontend;

use App\Models\Asset;
use App\Models\Collection as CollectionModel;
use App\Support\TemplateLayouts;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class CollectionIndex extends Component
{
    use WithPagination;

    public CollectionModel $collection;

    public function mount(string $collectionSlug): void
    {
        $this->collection = CollectionModel::query()
            ->where('slug', $collectionSlug)
            ->where('is_active', true)
            ->firstOrFail();
    }

    #[Layout('components.layouts.frontend')]
    public function render(): View|Factory
    {
        if (($this->collection->settings['type'] ?? 'standard') === 'single') {
            return $this->renderSingle();
        }

        $entries = $this->collection->entries()
            ->where('status', 'published')
            ->with(['elements.Field', 'author'])
            ->latest('published_at')
            ->paginate(9);

        $template = $this->collection->settings['index_template']
            ?? TemplateLayouts::defaultIndexTemplate();

        if (! array_key_exists($template, TemplateLayouts::indexTemplates())) {
            $template = TemplateLayouts::defaultIndexTemplate();
        }

        return view('livewire.frontend.collection-index', [
            'entries'  => $entries,
            'template' => $template,
            'theme'    => $this->collection->settings['theme'] ?? 'greenpeace',
            'isSingle' => false,
        ]);
    }

    private function renderSingle(): View|Factory
    {
        $entry = $this->collection->entries()
            ->where('status', 'published')
            ->with(['elements.field', 'blueprint'])
            ->latest('published_at')
            ->first();

        $sections = $entry?->getPageBuilderSections() ?? [];
        $assets   = $this->resolveAssets($sections);
        $theme    = $this->collection->settings['theme'] ?? 'greenpeace';

        return view('livewire.frontend.collection-index', [
            'entry'    => $entry,
            'sections' => $sections,
            'assets'   => $assets,
            'template' => 'landing-page',
            'theme'    => $theme,
            'isSingle' => true,
        ]);
    }

    /**
     * Batch-load assets referenced by image fields in page builder sections.
     */
    private function resolveAssets(array $sections): EloquentCollection
    {
        if (empty($sections)) {
            return new EloquentCollection;
        }

        $assetIds = collect($sections)
            ->flatMap(function (array $section): array {
                return match ($section['type']) {
                    'hero'       => [$section['data']['bg_image'] ?? null],
                    'image_text' => [$section['data']['image'] ?? null],
                    'gallery'    => $section['data']['images'] ?? [],
                    default      => [],
                };
            })
            ->filter()
            ->unique()
            ->values()
            ->all();

        if (empty($assetIds)) {
            return new EloquentCollection;
        }

        return Asset::query()->whereIn('id', $assetIds)->get();
    }

    public function title(): string
    {
        return $this->collection->name;
    }
}
```

- [ ] **Step 5: Update collection-index view to handle single mode**

Replace the full contents of `resources/views/livewire/frontend/collection-index.blade.php` with:

```blade
{{-- resources/views/livewire/frontend/collection-index.blade.php --}}
<div>
    @if($isSingle ?? false)
        <x-dynamic-component
            :component="'templates.detail.' . $template"
            :entry="$entry ?? null"
            :sections="$sections ?? []"
            :assets="$assets ?? collect()"
            :theme="$theme"
        />
    @else
        <x-dynamic-component
            :component="'templates.index.' . $template"
            :collection="$collection"
            :entries="$entries"
            :theme="$theme"
        />
    @endif
</div>
```

- [ ] **Step 6: Run tests to confirm they pass**

```bash
php artisan test --compact --filter=LandingPageTest
```

Expected: 4 passed.

- [ ] **Step 7: Run the full suite to catch regressions**

```bash
php artisan test --compact
```

Expected: all passing (no regressions on existing CollectionIndex tests).

- [ ] **Step 8: Run pint**

```bash
vendor/bin/pint --dirty --format agent
```

- [ ] **Step 9: Commit**

```bash
git add resources/views/components/templates/detail/landing-page.blade.php \
        app/Livewire/Frontend/CollectionIndex.php \
        resources/views/livewire/frontend/collection-index.blade.php \
        tests/Feature/Frontend/LandingPageTest.php
git commit -m "feat: add landing-page template and single-type collection rendering to CollectionIndex"
```

---

## Task 3: Refactor Home.php

**Files:**
- Modify: `app/Livewire/Frontend/Home.php`
- Modify: `resources/views/livewire/frontend/home.blade.php`
- Modify: `tests/Feature/Frontend/HomeSectionsTest.php`

- [ ] **Step 1: Update Home.php — rename resolveAssets, remove entry param**

Replace the full contents of `app/Livewire/Frontend/Home.php` with:

```php
<?php

namespace App\Livewire\Frontend;

use App\Models\Asset;
use App\Models\Entry;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class Home extends Component
{
    #[Layout('components.layouts.frontend')]
    #[Title('Home')]
    public function render(): View|Factory
    {
        $entry = Entry::query()
            ->where('slug', 'home')
            ->where('status', 'published')
            ->with(['elements.field', 'collection'])
            ->first();

        $sections = $entry?->getPageBuilderSections() ?? [];
        $assets   = $this->resolveAssets($sections);
        $theme    = $entry?->collection?->settings['theme'] ?? 'greenpeace';

        return view('livewire.frontend.home', [
            'entry'   => $entry,
            'layout'  => $sections,
            'assets'  => $assets,
            'theme'   => $theme,
        ]);
    }

    /**
     * Batch-load assets referenced by image fields in page builder sections.
     */
    private function resolveAssets(array $sections): Collection
    {
        if (empty($sections)) {
            return new Collection;
        }

        $assetIds = collect($sections)
            ->flatMap(function (array $section): array {
                return match ($section['type']) {
                    'hero'       => [$section['data']['bg_image'] ?? null],
                    'image_text' => [$section['data']['image'] ?? null],
                    'gallery'    => $section['data']['images'] ?? [],
                    default      => [],
                };
            })
            ->filter()
            ->unique()
            ->values()
            ->all();

        if (empty($assetIds)) {
            return new Collection;
        }

        return Asset::query()->whereIn('id', $assetIds)->get();
    }
}
```

- [ ] **Step 2: Update home.blade.php — remove legacy fallback block**

Replace the full contents of `resources/views/livewire/frontend/home.blade.php` with:

```blade
<div>
    @if ($entry && !empty($layout))
        @foreach ($layout as $section)
            <x-dynamic-component
                :component="'sections.' . str_replace('_', '-', $section['type'])"
                :section="$section"
                :assets="$assets"
                wire:key="section-{{ $section['_id'] }}"
            />
        @endforeach
    @else
        <x-themes.wrapper :theme="$theme">
            <div class="mx-auto max-w-3xl space-y-8 text-center">
                <flux:heading class="text-4xl!">{{ __('Welcome') }}</flux:heading>
                <flux:text>{{ __('Content coming soon...') }}</flux:text>
            </div>
        </x-themes.wrapper>
    @endif
</div>
```

- [ ] **Step 3: Remove the legacy fallback test from HomeSectionsTest**

In `tests/Feature/Frontend/HomeSectionsTest.php`, delete the entire test block:

```php
test('home page uses legacy element rendering when entry has no layout', function () {
    $entry = Entry::factory()->create([
        'slug' => 'home',
        'status' => 'published',
        'layout' => [],
    ]);

    $entry->elements()->create([
        'field_id' => Field::factory()->create()->id,
        'handle' => 'hero_title',
        'value' => 'Legacy Hero Title',
    ]);

    $this->get('/')->assertSee('Legacy Hero Title');
});
```

Also remove the `Field` import if it is now unused:

```php
use App\Models\Field;
```

- [ ] **Step 4: Run the Home tests**

```bash
php artisan test --compact --filter=HomeSectionsTest
```

Expected: 5 passed (was 6; the legacy test is removed).

- [ ] **Step 5: Run pint**

```bash
vendor/bin/pint --dirty --format agent
```

- [ ] **Step 6: Commit**

```bash
git add app/Livewire/Frontend/Home.php \
        resources/views/livewire/frontend/home.blade.php \
        tests/Feature/Frontend/HomeSectionsTest.php
git commit -m "refactor: simplify Home.php to use resolveAssets, remove legacy field fallback"
```

---

## Task 4: LandingPageWizardAgent

**Files:**
- Create: `app/Ai/Agents/LandingPageWizardAgent.php`

- [ ] **Step 1: Create the agent**

Create `app/Ai/Agents/LandingPageWizardAgent.php`:

```php
<?php

namespace App\Ai\Agents;

use App\Support\SectionTypes;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Attributes\UseSmartestModel;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Promptable;

#[UseSmartestModel]
class LandingPageWizardAgent implements Agent, HasStructuredOutput
{
    use Promptable;

    public function instructions(): string
    {
        $sectionDocs = collect(SectionTypes::all())
            ->map(function (array $type, string $key): string {
                $fields = collect($type['fields'])
                    ->map(fn (array $f, string $handle) => "    - {$handle} ({$f['type']}): {$f['label']}")
                    ->implode("\n");

                return "- **{$key}** ({$type['label']}):\n{$fields}";
            })
            ->implode("\n\n");

        return <<<INSTRUCTIONS
        You are a landing page content architect. Given a description of a landing page, produce an
        ordered list of page builder sections with pre-filled content.

        Available section types and their fields:

        {$sectionDocs}

        Rules:
        - Always start with a "hero" section.
        - Include 3–6 sections total. Choose types that make sense for the described page.
        - Fill all text fields with realistic, relevant content.
        - Set all image fields (bg_image, image, images) to null or [] — images are added manually later.
        - For "features" sections, include 3 feature items in the items array, each with icon (a
          Heroicon name like "bolt", "star", "check"), item_title, and item_description.
        - Each section must have a unique "_id" — use a short random-looking alphanumeric string
          (e.g., "a1b2c3").
        - Do not include page_builder, repeater, calendar, or time sections — these are field types,
          not section types.
        INSTRUCTIONS;
    }

    /**
     * Empty items array shape is intentional — feature items are runtime-dynamic.
     *
     * @return array<string, mixed>
     */
    public function schema(JsonSchema $schema): array
    {
        $dataShape = $schema->object([
            'title'               => $schema->string(),
            'subtitle'            => $schema->string(),
            'content'             => $schema->string(),
            'cta_text'            => $schema->string(),
            'cta_url'             => $schema->string(),
            'secondary_cta_text'  => $schema->string(),
            'secondary_cta_url'   => $schema->string(),
            'image_position'      => $schema->string(),
            'alignment'           => $schema->string(),
            'bg_image'            => $schema->string()->nullable(),
            'image'               => $schema->string()->nullable(),
            'images'              => $schema->array($schema->string()->nullable()),
            'items'               => $schema->array($schema->object([])->additionalProperties($schema->string())),
        ]);

        return [
            'sections' => $schema->array(
                $schema->object([
                    '_id'  => $schema->string()->required(),
                    'type' => $schema->string()->required(),
                    'data' => $dataShape->required(),
                ])
            )->required(),
        ];
    }
}
```

- [ ] **Step 2: Run pint**

```bash
vendor/bin/pint --dirty --format agent
```

- [ ] **Step 3: Commit**

```bash
git add app/Ai/Agents/LandingPageWizardAgent.php
git commit -m "feat: add LandingPageWizardAgent with structured output schema"
```

---

## Task 5: Landing Page AI Wizard component, view, route, and tests

**Files:**
- Create: `app/Livewire/LandingPages/AiWizard.php`
- Create: `resources/views/livewire/landing-pages/ai-wizard.blade.php`
- Modify: `routes/web.php`
- Modify: `resources/views/livewire/collections/index.blade.php`
- Create: `tests/Feature/LandingPages/LandingPageAiWizardTest.php`

- [ ] **Step 1: Write the failing tests**

```bash
php artisan make:test --pest LandingPages/LandingPageAiWizardTest
```

Edit `tests/Feature/LandingPages/LandingPageAiWizardTest.php`:

```php
<?php

use App\Ai\Agents\LandingPageWizardAgent;
use App\Livewire\LandingPages\AiWizard;
use App\Models\Blueprint;
use App\Models\Collection;
use App\Models\Entry;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    actingAs(User::factory()->create(['role' => 'admin']));
});

test('landing page ai wizard page loads', function () {
    $this->get(route('landing-pages.ai-wizard'))->assertOk();
});

test('generate sets proposal from ai agent', function () {
    LandingPageWizardAgent::fake([[
        'sections' => [
            [
                '_id'  => 'a1b2c3',
                'type' => 'hero',
                'data' => [
                    'title'              => 'Welcome',
                    'subtitle'           => 'We build things',
                    'content'            => '',
                    'cta_text'           => 'Get Started',
                    'cta_url'            => '/',
                    'secondary_cta_text' => '',
                    'secondary_cta_url'  => '',
                    'bg_image'           => null,
                    'image'              => null,
                    'images'             => [],
                    'image_position'     => 'left',
                    'alignment'          => 'left',
                    'items'              => [],
                ],
            ],
        ],
    ]]);

    Livewire::test(AiWizard::class)
        ->set('name', 'About Us')
        ->set('description', 'A page about our company and team')
        ->call('generate')
        ->assertSet('step', 'review')
        ->assertCount('proposal', 1);
});

test('remove section removes it from proposal', function () {
    Livewire::test(AiWizard::class)
        ->set('step', 'review')
        ->set('proposal', [
            ['_id' => 'a1', 'type' => 'hero', 'data' => ['title' => 'Hero']],
            ['_id' => 'b2', 'type' => 'cta',  'data' => ['title' => 'CTA']],
        ])
        ->call('removeSection', 0)
        ->assertCount('proposal', 1)
        ->assertSet('proposal.0.type', 'cta');
});

test('save creates collection blueprint and entry as draft', function () {
    LandingPageWizardAgent::fake([[
        'sections' => [
            [
                '_id'  => 'a1b2c3',
                'type' => 'hero',
                'data' => [
                    'title'              => 'Services',
                    'subtitle'           => '',
                    'content'            => '',
                    'cta_text'           => '',
                    'cta_url'            => '',
                    'secondary_cta_text' => '',
                    'secondary_cta_url'  => '',
                    'bg_image'           => null,
                    'image'              => null,
                    'images'             => [],
                    'image_position'     => 'left',
                    'alignment'          => 'left',
                    'items'              => [],
                ],
            ],
        ],
    ]]);

    Livewire::test(AiWizard::class)
        ->set('name', 'Services')
        ->set('description', 'Our services page')
        ->call('generate')
        ->call('save')
        ->assertRedirectContains('/entries/');

    $collection = Collection::where('slug', 'services')->first();
    expect($collection)->not->toBeNull();
    expect($collection->settings['type'])->toBe('single');

    $blueprint = Blueprint::find($collection->blueprint_id);
    expect($blueprint)->not->toBeNull();
    expect($blueprint->slug)->toBe('services-blueprint');

    $entry = Entry::where('slug', 'services')->first();
    expect($entry)->not->toBeNull();
    expect($entry->status)->toBe('draft');
});

it('denies access to viewer role users', function () {
    $viewer = User::factory()->create(['role' => 'viewer']);

    Livewire::actingAs($viewer)
        ->test(AiWizard::class)
        ->assertForbidden();
});
```

- [ ] **Step 2: Run tests to confirm they fail**

```bash
php artisan test --compact --filter=LandingPageAiWizardTest
```

Expected: failures — class `App\Livewire\LandingPages\AiWizard` not found.

- [ ] **Step 3: Create the Livewire component**

Create `app/Livewire/LandingPages/AiWizard.php`:

```php
<?php

namespace App\Livewire\LandingPages;

use App\Ai\Agents\LandingPageWizardAgent;
use App\Livewire\Actions\Blueprints\CreateBlueprint;
use App\Livewire\Actions\CreateEntry;
use App\Models\Collection;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
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
            'name'        => 'required|string|min:2|max:255',
            'slug'        => 'required|string|max:255|unique:collections,slug',
            'description' => 'required|string|min:10|max:1000',
        ]);

        $response = LandingPageWizardAgent::make()->prompt($this->description);

        $this->proposal = $response['sections'];
        $this->step     = 'review';
    }

    public function removeSection(int $index): void
    {
        $sections = $this->proposal;
        array_splice($sections, $index, 1);
        $this->proposal = array_values($sections);
    }

    public function save(): void
    {
        // 1. Create collection (blueprint_id set after blueprint is created)
        $collection = app(\App\Livewire\Actions\Collections\CreateCollection::class)->execute([
            'name'      => $this->name,
            'slug'      => $this->slug,
            'is_active' => true,
            'settings'  => ['type' => 'single'],
        ]);

        // 2. Create blueprint with one page_builder field
        $blueprint = app(CreateBlueprint::class)->create([
            'name'        => $this->name . ' Blueprint',
            'slug'        => $this->slug . '-blueprint',
            'description' => '',
            'is_active'   => true,
            'tabs'        => [
                [
                    'name'       => 'Content',
                    'handle'     => 'content',
                    'sortOrder'  => 0,
                    'sections'   => [
                        [
                            'name'         => 'Page Builder',
                            'handle'       => 'page_builder',
                            'instructions' => '',
                            'sortOrder'    => 0,
                            'fields'       => [
                                [
                                    'label'        => 'Page Sections',
                                    'handle'       => 'page_sections',
                                    'type'         => 'page_builder',
                                    'instructions' => '',
                                    'is_required'  => false,
                                    'config'       => [],
                                    'sortOrder'    => 0,
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
        $entry = app(CreateEntry::class)->handle([
            'title'        => $this->name,
            'slug'         => $this->slug,
            'blueprint_id' => $blueprint->id,
            'status'       => 'draft',
            'fieldsValues' => [
                [
                    'field_id' => $pageBuilderField->id,
                    'handle'   => 'page_sections',
                    'type'     => 'page_builder',
                    'value'    => $this->proposal,
                    'children' => [],
                ],
            ],
        ]);

        $this->redirect(route('entries.edit', $entry), navigate: true);
    }

    #[Layout('components.layouts.app')]
    public function render(): View|Factory
    {
        return view('livewire.landing-pages.ai-wizard');
    }
}
```

- [ ] **Step 4: Create the wizard view**

Create `resources/views/livewire/landing-pages/ai-wizard.blade.php`:

```blade
<div class="mx-auto max-w-4xl flex flex-col gap-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('Landing Page AI Wizard') }}</flux:heading>
            <flux:text>{{ __('Describe your page and let AI generate the section layout.') }}</flux:text>
        </div>
        <flux:button icon="arrow-uturn-left" wire:navigate href="{{ route('collections.index') }}">
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
            <flux:heading size="lg">{{ __('What is this page for?') }}</flux:heading>

            <flux:input
                label="{{ __('Page Name') }}"
                wire:model.live="name"
                placeholder="{{ __('About Us') }}"
                badge="{{ __('Required') }}"
                description="{{ __('Used as the page title and to generate the URL slug.') }}"
            />
            <flux:error name="name" />

            <flux:input
                label="{{ __('Slug') }}"
                wire:model="slug"
                placeholder="{{ __('about-us') }}"
                description="{{ __('Auto-generated from the name. Must be unique.') }}"
            />
            <flux:error name="slug" />

            <flux:textarea
                wire:model="description"
                label="{{ __('Page Description') }}"
                rows="5"
                placeholder="{{ __('A page introducing our company, team, values, and a call to action to get in touch.') }}"
                description="{{ __('Describe the purpose and content of this page. The more detail, the better the result.') }}"
            />
            <flux:error name="description" />
        </flux:card>

        <div class="flex justify-end">
            <flux:button
                variant="primary"
                wire:click="generate"
                wire:loading.attr="disabled"
                icon="sparkles"
            >
                <span wire:loading.remove wire:target="generate">{{ __('Generate Page') }}</span>
                <span wire:loading wire:target="generate">{{ __('Generating…') }}</span>
            </flux:button>
        </div>
    @endif

    {{-- Step 2: Review --}}
    @if($step === 'review' && !empty($proposal))
        <flux:callout variant="info" icon="information-circle">
            {{ __('Review the proposed sections. You can remove any you don\'t want. Images can be added after saving.') }}
        </flux:callout>

        <flux:card class="p-0! divide-y divide-zinc-200 dark:divide-zinc-700">
            <div class="px-6 py-4">
                <flux:heading size="lg">{{ $name }}</flux:heading>
                <flux:text class="text-sm text-zinc-500">{{ $slug }}</flux:text>
            </div>

            @foreach($proposal as $index => $section)
                <div class="flex items-center justify-between px-6 py-4">
                    <div class="flex items-center gap-3 min-w-0">
                        <flux:badge variant="pill" color="teal">{{ $section['type'] }}</flux:badge>
                        @if(!empty($section['data']['title']))
                            <flux:text class="truncate text-sm">{{ $section['data']['title'] }}</flux:text>
                        @elseif(!empty($section['data']['content']))
                            <flux:text class="truncate text-sm text-zinc-400 italic">
                                {{ Str::limit(strip_tags($section['data']['content']), 60) }}
                            </flux:text>
                        @endif
                    </div>
                    <flux:button
                        size="sm"
                        variant="ghost"
                        icon="trash"
                        wire:click="removeSection({{ $index }})"
                        class="text-red-500 hover:text-red-600 shrink-0"
                    />
                </div>
            @endforeach
        </flux:card>

        <div class="flex items-center justify-between">
            <flux:button variant="ghost" wire:click="$set('step', 'describe')">
                {{ __('← Back') }}
            </flux:button>
            <flux:button
                variant="primary"
                wire:click="save"
                wire:loading.attr="disabled"
                icon="check"
            >
                <span wire:loading.remove wire:target="save">{{ __('Save as Draft') }}</span>
                <span wire:loading wire:target="save">{{ __('Saving…') }}</span>
            </flux:button>
        </div>
    @endif
</div>
```

- [ ] **Step 5: Add route to web.php**

In `routes/web.php`, inside the `Route::middleware(['auth'])` group, add after the Collections routes and before or alongside the Blueprints routes:

```php
// Landing Pages Routes
Route::get('landing-pages/ai-wizard', \App\Livewire\LandingPages\AiWizard::class)->name('landing-pages.ai-wizard');
```

- [ ] **Step 6: Add "Create with AI" button to collections index**

In `resources/views/livewire/collections/index.blade.php`, find the header actions div:

```blade
<flux:button icon="plus" wire:navigate href="{{ route('collections.create') }}" variant="primary">
    {{ __('Create Collection') }}
</flux:button>
```

Replace with:

```blade
<div class="flex gap-2">
    <flux:button icon="sparkles" wire:navigate href="{{ route('landing-pages.ai-wizard') }}" variant="ghost">
        {{ __('Create with AI') }}
    </flux:button>
    <flux:button icon="plus" wire:navigate href="{{ route('collections.create') }}" variant="primary">
        {{ __('Create Collection') }}
    </flux:button>
</div>
```

- [ ] **Step 7: Run tests to confirm they pass**

```bash
php artisan test --compact --filter=LandingPageAiWizardTest
```

Expected: 5 passed.

- [ ] **Step 8: Run the full suite**

```bash
php artisan test --compact
```

Expected: all passing.

- [ ] **Step 9: Run pint**

```bash
vendor/bin/pint --dirty --format agent
```

- [ ] **Step 10: Commit**

```bash
git add app/Livewire/LandingPages/AiWizard.php \
        resources/views/livewire/landing-pages/ai-wizard.blade.php \
        routes/web.php \
        resources/views/livewire/collections/index.blade.php \
        tests/Feature/LandingPages/LandingPageAiWizardTest.php
git commit -m "feat: add Landing Page AI Wizard — creates collection, blueprint, and draft entry in one flow"
```

---

## Task 6: Final verification

- [ ] **Step 1: Run the full test suite**

```bash
php artisan test --compact
```

Expected: all passing, 0 failed.

- [ ] **Step 2: Run pint on the whole project**

```bash
vendor/bin/pint --format agent
```

Fix any remaining style issues.

- [ ] **Step 3: Verify routes**

```bash
php artisan route:list --name=landing-pages
```

Expected output includes: `GET landing-pages/ai-wizard` → `landing-pages.ai-wizard`

- [ ] **Step 4: Commit any pint fixes**

If pint made changes:

```bash
git add -A
git commit -m "style: apply pint formatting"
```
