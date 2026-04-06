# Dynamic Frontend Template System — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Replace four hardcoded frontend Livewire components with two generic ones (`CollectionIndex`, `EntryShow`) that render any collection/entry using selectable layout templates and per-field-type Blade renderers.

**Architecture:** A `TemplateLayouts` support class registers available templates. `CollectionIndex` and `EntryShow` resolve the correct template from `collection.settings.index_template` and `blueprint.settings.detail_template`. Detail templates loop through blueprint fields and delegate rendering to per-type components in `resources/views/components/field-renderers/`. Both templates use Flux UI components throughout.

**Tech Stack:** Laravel 12, Livewire 4, Flux UI v2, Tailwind CSS v4, Pest v4

---

## File Map

**Create:**
- `database/migrations/xxxx_add_settings_to_blueprints_table.php`
- `app/Support/TemplateLayouts.php`
- `app/Livewire/Frontend/CollectionIndex.php`
- `resources/views/livewire/frontend/collection-index.blade.php`
- `app/Livewire/Frontend/EntryShow.php`
- `resources/views/livewire/frontend/entry-show.blade.php`
- `resources/views/components/templates/index/card-grid.blade.php`
- `resources/views/components/templates/index/list.blade.php`
- `resources/views/components/templates/index/magazine.blade.php`
- `resources/views/components/templates/detail/article.blade.php`
- `resources/views/components/templates/detail/full-width.blade.php`
- `resources/views/components/templates/detail/minimal.blade.php`
- `resources/views/components/field-renderers/text.blade.php`
- `resources/views/components/field-renderers/richtext.blade.php`
- `resources/views/components/field-renderers/image.blade.php`
- `resources/views/components/field-renderers/url.blade.php`
- `resources/views/components/field-renderers/email.blade.php`
- `resources/views/components/field-renderers/date.blade.php`
- `resources/views/components/field-renderers/toggle.blade.php`
- `resources/views/components/field-renderers/select.blade.php`
- `resources/views/components/field-renderers/number.blade.php`
- `resources/views/components/field-renderers/file.blade.php`
- `resources/views/components/field-renderers/repeater.blade.php`
- `tests/Feature/Frontend/CollectionIndexTest.php`
- `tests/Feature/Frontend/EntryShowTest.php`

**Modify:**
- `app/Models/Blueprint.php` — add `settings` to fillable and casts
- `app/Livewire/Forms/CollectionForm.php` — add `$index_template`
- `app/Livewire/Forms/BlueprintForm.php` — add `$detail_template`
- `resources/views/livewire/collections/create.blade.php` — add template dropdown
- `resources/views/livewire/collections/edit.blade.php` — add template dropdown
- `resources/views/livewire/blueprints/edit.blade.php` — add template dropdown
- `routes/web.php` — replace hardcoded routes, add generic routes

**Delete:**
- `app/Livewire/Frontend/BlogIndex.php`
- `app/Livewire/Frontend/BlogShow.php`
- `app/Livewire/Frontend/PortfolioIndex.php`
- `app/Livewire/Frontend/ContactPage.php`
- `resources/views/livewire/frontend/blog-index.blade.php`
- `resources/views/livewire/frontend/blog-show.blade.php`
- `resources/views/livewire/frontend/portfolio-index.blade.php`
- `resources/views/livewire/frontend/contact-page.blade.php`

---

## Task 1: Blueprint settings migration + model update

**Files:**
- Create: `database/migrations/xxxx_add_settings_to_blueprints_table.php`
- Modify: `app/Models/Blueprint.php`

- [ ] **Step 1: Generate the migration**

```bash
php artisan make:migration add_settings_to_blueprints_table --no-interaction
```

- [ ] **Step 2: Write the migration**

Open the generated file and replace its `up`/`down` with:

```php
public function up(): void
{
    Schema::table('blueprints', function (Blueprint $table): void {
        $table->json('settings')->nullable()->after('is_active');
    });
}

public function down(): void
{
    Schema::table('blueprints', function (Blueprint $table): void {
        $table->dropColumn('settings');
    });
}
```

- [ ] **Step 3: Run the migration**

```bash
php artisan migrate --no-interaction
```

Expected: `Running migrations... DONE`

- [ ] **Step 4: Update `Blueprint` model**

In `app/Models/Blueprint.php`, add `settings` to `$fillable`:

```php
protected $fillable = [
    'name',
    'slug',
    'description',
    'is_active',
    'settings',
];
```

And add the cast in `casts()`:

```php
protected function casts(): array
{
    return [
        'is_active' => 'boolean',
        'settings' => 'array',
    ];
}
```

- [ ] **Step 5: Write the failing test**

Create `tests/Feature/Frontend/BlueprintSettingsTest.php`:

```php
<?php

use App\Models\Blueprint;

test('blueprint can store and read settings', function () {
    $blueprint = Blueprint::factory()->create([
        'settings' => ['detail_template' => 'article'],
    ]);

    expect($blueprint->fresh()->settings['detail_template'])->toBe('article');
});
```

- [ ] **Step 6: Run the test**

```bash
php artisan test --compact --filter=BlueprintSettingsTest
```

Expected: PASS

- [ ] **Step 7: Commit**

```bash
git add database/migrations/ app/Models/Blueprint.php tests/Feature/Frontend/BlueprintSettingsTest.php
git commit -m "feat: add settings column to blueprints table"
```

---

## Task 2: TemplateLayouts support class

**Files:**
- Create: `app/Support/TemplateLayouts.php`

- [ ] **Step 1: Create the class**

```php
<?php

namespace App\Support;

class TemplateLayouts
{
    /**
     * @return array<string, string>
     */
    public static function indexTemplates(): array
    {
        return [
            'card-grid' => 'Card Grid',
            'list'      => 'List',
            'magazine'  => 'Magazine',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function detailTemplates(): array
    {
        return [
            'article'    => 'Article',
            'full-width' => 'Full Width',
            'minimal'    => 'Minimal',
        ];
    }

    public static function defaultIndexTemplate(): string
    {
        return 'card-grid';
    }

    public static function defaultDetailTemplate(): string
    {
        return 'article';
    }
}
```

- [ ] **Step 2: Commit**

```bash
git add app/Support/TemplateLayouts.php
git commit -m "feat: add TemplateLayouts support class"
```

---

## Task 3: Admin form + view updates (collection and blueprint)

**Files:**
- Modify: `app/Livewire/Forms/CollectionForm.php`
- Modify: `app/Livewire/Forms/BlueprintForm.php`
- Modify: `resources/views/livewire/collections/create.blade.php`
- Modify: `resources/views/livewire/collections/edit.blade.php`
- Modify: `resources/views/livewire/blueprints/edit.blade.php`

- [ ] **Step 1: Write the failing tests**

Create `tests/Feature/Frontend/TemplateSelectorTest.php`:

```php
<?php

use App\Livewire\Collections\Create as CollectionsCreate;
use App\Livewire\Collections\Edit as CollectionsEdit;
use App\Livewire\Blueprints\Edit as BlueprintsEdit;
use App\Models\Blueprint;
use App\Models\Collection;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    actingAs(User::factory()->create(['role' => 'admin']));
});

test('collection create saves index_template in settings', function () {
    $blueprint = Blueprint::factory()->create();

    Livewire::test(CollectionsCreate::class)
        ->set('form.name', 'Blog')
        ->set('form.blueprint_id', $blueprint->id)
        ->set('form.index_template', 'magazine')
        ->call('save')
        ->assertHasNoErrors();

    expect(Collection::where('name', 'Blog')->first()->settings['index_template'])->toBe('magazine');
});

test('collection edit saves index_template in settings', function () {
    $collection = Collection::factory()->create(['settings' => ['index_template' => 'card-grid']]);

    Livewire::test(CollectionsEdit::class, ['collection' => $collection])
        ->set('form.index_template', 'list')
        ->call('save')
        ->assertHasNoErrors();

    expect($collection->fresh()->settings['index_template'])->toBe('list');
});

test('blueprint edit saves detail_template in settings', function () {
    $blueprint = Blueprint::factory()->create();

    Livewire::test(BlueprintsEdit::class, ['blueprint' => $blueprint])
        ->set('form.detail_template', 'full-width')
        ->call('save')
        ->assertHasNoErrors();

    expect($blueprint->fresh()->settings['detail_template'])->toBe('full-width');
});
```

- [ ] **Step 2: Run tests to confirm they fail**

```bash
php artisan test --compact --filter=TemplateSelectorTest
```

Expected: FAIL (property `index_template` / `detail_template` not found)

- [ ] **Step 3: Update `CollectionForm`**

In `app/Livewire/Forms/CollectionForm.php`, add the property after the `$theme` property:

```php
#[Validate('nullable|string')]
public string $index_template = '';
```

Update `setCollection()` to populate it:

```php
public function setCollection($collection): void
{
    $this->collection_id = $collection->id;
    $this->name = $collection->name;
    $this->slug = $collection->slug;
    $this->description = $collection->description ?? '';
    $this->blueprint_id = $collection->blueprint_id;
    $this->is_active = $collection->is_active;
    $this->theme = $collection->settings['theme'] ?? '';
    $this->index_template = $collection->settings['index_template'] ?? '';
}
```

Update `create()` to include it:

```php
$collection = app(CreateCollection::class)->execute([
    'name' => $this->name,
    'slug' => $this->slug,
    'description' => $this->description,
    'blueprint_id' => $this->blueprint_id,
    'is_active' => $this->is_active,
    'settings' => array_filter([
        'theme' => $this->theme ?: null,
        'index_template' => $this->index_template ?: null,
    ]),
]);
```

Update `update()` to include it:

```php
$updated = app(UpdateCollection::class)->execute($collection, [
    'name' => $this->name,
    'slug' => $this->slug,
    'description' => $this->description,
    'blueprint_id' => $this->blueprint_id,
    'is_active' => $this->is_active,
    'settings' => array_merge($collection->settings ?? [], array_filter([
        'theme' => $this->theme ?: null,
        'index_template' => $this->index_template ?: null,
    ])),
]);
```

Add `'index_template'` to the `reset()` call in `create()`:

```php
$this->reset('name', 'slug', 'description', 'blueprint_id', 'is_active', 'theme', 'index_template');
```

- [ ] **Step 4: Update `BlueprintForm`**

In `app/Livewire/Forms/BlueprintForm.php`, add the property after `$is_active`:

```php
#[Validate('nullable|string')]
public string $detail_template = '';
```

Update `setBlueprint()` to populate it:

```php
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
```

Update `update()` to include it:

```php
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
```

- [ ] **Step 5: Update `UpdateBlueprint` action to persist settings**

In `app/Livewire/Actions/Blueprints/UpdateBlueprint.php`, replace the existing `$blueprint->update([...])` call with:

```php
$blueprint->update([
    'name'        => $blueprintData['name'],
    'slug'        => $blueprintData['slug'],
    'description' => $blueprintData['description'] ?? null,
    'is_active'   => $blueprintData['is_active'] ?? false,
    'settings'    => array_merge($blueprint->settings ?? [], $blueprintData['settings'] ?? []),
]);
```

- [ ] **Step 6: Add dropdown to collection create view**

In `resources/views/livewire/collections/create.blade.php`, add after the Theme `<flux:select>` block:

```blade
{{-- Index Template --}}
<flux:select label="{{ __('Index Template') }}" wire:model="form.index_template" description="{{ __('Layout used for the collection listing page') }}">
    <flux:select.option value="">{{ __('Default (Card Grid)') }}</flux:select.option>
    @foreach (\App\Support\TemplateLayouts::indexTemplates() as $value => $label)
        <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
    @endforeach
</flux:select>
```

- [ ] **Step 7: Add dropdown to collection edit view**

In `resources/views/livewire/collections/edit.blade.php`, add the same block after the Theme `<flux:select>`:

```blade
{{-- Index Template --}}
<flux:select label="{{ __('Index Template') }}" wire:model="form.index_template" description="{{ __('Layout used for the collection listing page') }}">
    <flux:select.option value="">{{ __('Default (Card Grid)') }}</flux:select.option>
    @foreach (\App\Support\TemplateLayouts::indexTemplates() as $value => $label)
        <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
    @endforeach
</flux:select>
```

- [ ] **Step 8: Add dropdown to blueprint edit view**

In `resources/views/livewire/blueprints/edit.blade.php`, find the Status switch block and add before it:

```blade
{{-- Detail Template --}}
<div class="px-6">
    <flux:select label="{{ __('Detail Template') }}" wire:model="form.detail_template" description="{{ __('Layout used when displaying an individual entry') }}">
        <flux:select.option value="">{{ __('Default (Article)') }}</flux:select.option>
        @foreach (\App\Support\TemplateLayouts::detailTemplates() as $value => $label)
            <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
        @endforeach
    </flux:select>
</div>
<flux:separator />
```

- [ ] **Step 9: Run the tests**

```bash
php artisan test --compact --filter=TemplateSelectorTest
```

Expected: PASS (3 tests)

- [ ] **Step 10: Format and commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Livewire/Forms/CollectionForm.php app/Livewire/Forms/BlueprintForm.php \
  app/Livewire/Actions/Blueprints/UpdateBlueprint.php \
  resources/views/livewire/collections/ resources/views/livewire/blueprints/edit.blade.php \
  app/Support/TemplateLayouts.php tests/Feature/Frontend/TemplateSelectorTest.php
git commit -m "feat: add index_template and detail_template selectors to collection and blueprint forms"
```

---

## Task 4: Field renderer Blade components

**Files:**
- Create: `resources/views/components/field-renderers/*.blade.php` (11 files)

Each component receives two props: `$field` (`App\Models\Field`) and `$value` (the decoded element value). They silently skip rendering when `$value` is `null` or empty.

- [ ] **Step 1: Create `text.blade.php`**

```blade
@props(['field', 'value'])
@if($value)
    <p class="text-zinc-700 dark:text-zinc-300">{{ $value }}</p>
@endif
```

- [ ] **Step 2: Create `richtext.blade.php`**

```blade
@props(['field', 'value'])
@if($value)
    <div class="prose prose-lg dark:prose-invert max-w-none">{!! $value !!}</div>
@endif
```

- [ ] **Step 3: Create `image.blade.php`**

```blade
@props(['field', 'value'])
@if($value)
    <img src="{{ $value }}" alt="{{ $field->label }}" class="w-full rounded-lg object-cover" />
@endif
```

- [ ] **Step 4: Create `url.blade.php`**

```blade
@props(['field', 'value'])
@if($value)
    <flux:link href="{{ $value }}" target="_blank" class="break-all">{{ $value }}</flux:link>
@endif
```

- [ ] **Step 5: Create `email.blade.php`**

```blade
@props(['field', 'value'])
@if($value)
    <flux:link href="mailto:{{ $value }}">{{ $value }}</flux:link>
@endif
```

- [ ] **Step 6: Create `date.blade.php`**

```blade
@props(['field', 'value'])
@if($value)
    <flux:text>{{ \Carbon\Carbon::parse($value)->format('F j, Y') }}</flux:text>
@endif
```

- [ ] **Step 7: Create `toggle.blade.php`**

```blade
@props(['field', 'value'])
<flux:badge variant="{{ $value ? 'success' : 'zinc' }}">
    {{ $value ? ($field->config['on_label'] ?? 'Yes') : ($field->config['off_label'] ?? 'No') }}
</flux:badge>
```

- [ ] **Step 8: Create `select.blade.php`** (also handles `radio`)

```blade
@props(['field', 'value'])
@if($value)
    @php
        $label = collect($field->config['options'] ?? [])->firstWhere('value', $value)['label'] ?? $value;
    @endphp
    <flux:badge>{{ $label }}</flux:badge>
@endif
```

- [ ] **Step 9: Create `number.blade.php`**

```blade
@props(['field', 'value'])
@if($value !== null && $value !== '')
    <flux:text>{{ $value }}</flux:text>
@endif
```

- [ ] **Step 10: Create `file.blade.php`**

```blade
@props(['field', 'value'])
@if($value)
    <flux:link href="{{ $value }}" target="_blank" download>
        {{ basename($value) }}
    </flux:link>
@endif
```

- [ ] **Step 11: Create `repeater.blade.php`**

```blade
@props(['field', 'value'])
@if($value && is_array($value))
    <div class="space-y-4">
        @foreach($value as $item)
            <flux:card class="space-y-2">
                @foreach($field->children as $childField)
                    @php $childValue = $item[$childField->handle] ?? null; @endphp
                    @if($childValue !== null && $childValue !== '')
                        <div>
                            <flux:text class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ $childField->label }}</flux:text>
                            <x-dynamic-component
                                :component="'field-renderers.' . $childField->type"
                                :field="$childField"
                                :value="$childValue"
                            />
                        </div>
                    @endif
                @endforeach
            </flux:card>
        @endforeach
    </div>
@endif
```

- [ ] **Step 12: Commit**

```bash
git add resources/views/components/field-renderers/
git commit -m "feat: add field renderer Blade components for all field types"
```

---

## Task 5: Index template Blade components

**Files:**
- Create: `resources/views/components/templates/index/card-grid.blade.php`
- Create: `resources/views/components/templates/index/list.blade.php`
- Create: `resources/views/components/templates/index/magazine.blade.php`

Each template receives:
- `$collection` — `App\Models\Collection`
- `$entries` — `LengthAwarePaginator` of `Entry` models (with `elements.field` and `author` eager-loaded)
- `$theme` — string (e.g. `'greenpeace'`)

The `route('entry.show', [$collection->slug, $entry->slug])` pattern is used for links.

- [ ] **Step 1: Create `card-grid.blade.php`**

```blade
@props(['collection', 'entries', 'theme' => 'greenpeace'])
<x-themes.wrapper :theme="$theme">
    <div class="text-center mb-12">
        <flux:heading size="xl" class="text-white!">{{ $collection->name }}</flux:heading>
        @if($collection->description)
            <flux:text class="mt-4 text-zinc-300!">{{ $collection->description }}</flux:text>
        @endif
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($entries as $entry)
            @php
                $featuredImage = $entry->elements->firstWhere('handle', 'featured_image')?->getElementValue();
                $excerpt = $entry->elements->first(fn($el) => in_array($el->Field?->type, ['textarea', 'text']) && $el->getElementValue())?->getElementValue();
            @endphp
            <flux:card class="p-0! overflow-hidden hover:shadow-xl transition">
                @if($featuredImage)
                    <img src="{{ $featuredImage }}" alt="{{ $entry->title }}" class="w-full h-48 object-cover">
                @endif
                <div class="p-6 space-y-3">
                    <flux:heading size="lg">
                        <a href="{{ route('entry.show', [$collection->slug, $entry->slug]) }}" wire:navigate class="hover:text-indigo-600 dark:hover:text-indigo-400">
                            {{ $entry->title }}
                        </a>
                    </flux:heading>
                    @if($excerpt)
                        <flux:text class="line-clamp-3">{{ $excerpt }}</flux:text>
                    @endif
                    <div class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400">
                        @if($entry->author)
                            <span>{{ $entry->author->name }}</span>
                            <span>•</span>
                        @endif
                        @if($entry->published_at)
                            <span>{{ $entry->published_at->format('M d, Y') }}</span>
                        @endif
                    </div>
                </div>
            </flux:card>
        @empty
            <div class="col-span-3 text-center py-12">
                <flux:text>No entries available yet.</flux:text>
            </div>
        @endforelse
    </div>

    <div class="mt-12">{{ $entries->links() }}</div>
</x-themes.wrapper>
```

- [ ] **Step 2: Create `list.blade.php`**

```blade
@props(['collection', 'entries', 'theme' => 'greenpeace'])
<x-themes.wrapper :theme="$theme">
    <div class="mb-10">
        <flux:heading size="xl" class="text-white!">{{ $collection->name }}</flux:heading>
        @if($collection->description)
            <flux:text class="mt-2 text-zinc-300!">{{ $collection->description }}</flux:text>
        @endif
    </div>

    <div class="space-y-4">
        @forelse($entries as $entry)
            @php
                $featuredImage = $entry->elements->firstWhere('handle', 'featured_image')?->getElementValue();
                $excerpt = $entry->elements->first(fn($el) => in_array($el->Field?->type, ['textarea', 'text']) && $el->getElementValue())?->getElementValue();
            @endphp
            <flux:card>
                <div class="flex gap-4 items-start">
                    @if($featuredImage)
                        <img src="{{ $featuredImage }}" alt="{{ $entry->title }}" class="w-20 h-20 object-cover rounded-lg shrink-0">
                    @endif
                    <div class="flex-1 min-w-0">
                        <flux:heading size="md">
                            <a href="{{ route('entry.show', [$collection->slug, $entry->slug]) }}" wire:navigate class="hover:text-indigo-600 dark:hover:text-indigo-400">
                                {{ $entry->title }}
                            </a>
                        </flux:heading>
                        @if($excerpt)
                            <flux:text class="mt-1 line-clamp-2">{{ $excerpt }}</flux:text>
                        @endif
                        <flux:text class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">
                            @if($entry->author) {{ $entry->author->name }} • @endif
                            @if($entry->published_at) {{ $entry->published_at->format('M d, Y') }} @endif
                        </flux:text>
                    </div>
                </div>
            </flux:card>
        @empty
            <flux:text>No entries available yet.</flux:text>
        @endforelse
    </div>

    <div class="mt-8">{{ $entries->links() }}</div>
</x-themes.wrapper>
```

- [ ] **Step 3: Create `magazine.blade.php`**

```blade
@props(['collection', 'entries', 'theme' => 'greenpeace'])
<x-themes.wrapper :theme="$theme">
    <div class="mb-10">
        <flux:heading size="xl" class="text-white!">{{ $collection->name }}</flux:heading>
        @if($collection->description)
            <flux:text class="mt-2 text-zinc-300!">{{ $collection->description }}</flux:text>
        @endif
    </div>

    @php $featured = $entries->first(); $rest = $entries->slice(1); @endphp

    @if($featured)
        @php
            $featuredImage = $featured->elements->firstWhere('handle', 'featured_image')?->getElementValue();
            $excerpt = $featured->elements->first(fn($el) => in_array($el->Field?->type, ['textarea', 'text']) && $el->getElementValue())?->getElementValue();
        @endphp
        <flux:card class="p-0! overflow-hidden mb-8">
            @if($featuredImage)
                <img src="{{ $featuredImage }}" alt="{{ $featured->title }}" class="w-full h-72 object-cover">
            @endif
            <div class="p-8 space-y-3">
                <flux:heading size="xl">
                    <a href="{{ route('entry.show', [$collection->slug, $featured->slug]) }}" wire:navigate class="hover:text-indigo-600 dark:hover:text-indigo-400">
                        {{ $featured->title }}
                    </a>
                </flux:heading>
                @if($excerpt)
                    <flux:text class="text-lg line-clamp-3">{{ $excerpt }}</flux:text>
                @endif
                <flux:text class="text-sm text-zinc-500 dark:text-zinc-400">
                    @if($featured->author) {{ $featured->author->name }} • @endif
                    @if($featured->published_at) {{ $featured->published_at->format('M d, Y') }} @endif
                </flux:text>
            </div>
        </flux:card>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach($rest as $entry)
            @php
                $excerpt = $entry->elements->first(fn($el) => in_array($el->Field?->type, ['textarea', 'text']) && $el->getElementValue())?->getElementValue();
            @endphp
            <flux:card>
                <flux:heading size="md">
                    <a href="{{ route('entry.show', [$collection->slug, $entry->slug]) }}" wire:navigate class="hover:text-indigo-600 dark:hover:text-indigo-400">
                        {{ $entry->title }}
                    </a>
                </flux:heading>
                @if($excerpt)
                    <flux:text class="mt-2 line-clamp-2">{{ $excerpt }}</flux:text>
                @endif
                <flux:text class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">
                    @if($entry->published_at) {{ $entry->published_at->format('M d, Y') }} @endif
                </flux:text>
            </flux:card>
        @endforeach
    </div>

    @if($entries->hasPages())
        <div class="mt-8">{{ $entries->links() }}</div>
    @endif
</x-themes.wrapper>
```

- [ ] **Step 4: Commit**

```bash
git add resources/views/components/templates/index/
git commit -m "feat: add card-grid, list, and magazine index templates"
```

---

## Task 6: Detail template Blade components

**Files:**
- Create: `resources/views/components/templates/detail/article.blade.php`
- Create: `resources/views/components/templates/detail/full-width.blade.php`
- Create: `resources/views/components/templates/detail/minimal.blade.php`

Each receives `$entry` (with `elements.field`, `blueprint.tabs.sections.fields`, `author` eager-loaded) and `$theme`.

The `$fields` variable is computed inline: all fields from the blueprint in tab → section → field order, skipping `page_builder` fields.

- [ ] **Step 1: Create `article.blade.php`**

```blade
@props(['entry', 'theme' => 'greenpeace'])
@php
    $fields = $entry->blueprint->tabs->sortBy('sort_order')
        ->flatMap(fn($tab) => $tab->sections->sortBy('sort_order')
            ->flatMap(fn($section) => $section->fields->sortBy('order')))
        ->reject(fn($field) => $field->type === 'page_builder');

    $firstImage = $fields->first(fn($f) => $f->type === 'image');
    $firstImageValue = $firstImage
        ? $entry->elements->firstWhere('handle', $firstImage->handle)?->getElementValue()
        : null;
@endphp

<x-themes.wrapper :theme="$theme">
    <div class="mx-auto max-w-3xl">
        {{-- Meta --}}
        <div class="mb-8">
            <flux:heading size="xl" class="text-white!">{{ $entry->title }}</flux:heading>
            <flux:text class="mt-3 text-zinc-400!">
                @if($entry->author) {{ $entry->author->name }} @endif
                @if($entry->published_at) • {{ $entry->published_at->format('F j, Y') }} @endif
            </flux:text>
        </div>

        {{-- Featured image --}}
        @if($firstImageValue)
            <img src="{{ $firstImageValue }}" alt="{{ $entry->title }}" class="w-full h-80 object-cover rounded-xl mb-8">
        @endif

        {{-- Fields --}}
        <div class="space-y-6">
            @foreach($fields as $field)
                @if($field->type === 'image') @continue @endif
                @php $value = $entry->elements->firstWhere('handle', $field->handle)?->getElementValue(); @endphp
                @if($value !== null && $value !== '')
                    <x-dynamic-component
                        :component="'field-renderers.' . $field->type"
                        :field="$field"
                        :value="$value"
                    />
                @endif
            @endforeach
        </div>

        {{-- Page builder sections --}}
        @foreach($entry->getPageBuilderSections() as $section)
            <x-dynamic-component
                :component="'sections.' . str_replace('_', '-', $section['type'])"
                :section="$section"
                :assets="collect()"
                wire:key="section-{{ $section['_id'] }}"
            />
        @endforeach

        <div class="mt-12">
            <flux:link href="javascript:history.back()" wire:navigate.back>← Back</flux:link>
        </div>
    </div>
</x-themes.wrapper>
```

- [ ] **Step 2: Create `full-width.blade.php`**

```blade
@props(['entry', 'theme' => 'greenpeace'])
@php
    $fields = $entry->blueprint->tabs->sortBy('sort_order')
        ->flatMap(fn($tab) => $tab->sections->sortBy('sort_order')
            ->flatMap(fn($section) => $section->fields->sortBy('order')))
        ->reject(fn($field) => $field->type === 'page_builder');

    $heroImage = $fields->first(fn($f) => $f->type === 'image');
    $heroImageValue = $heroImage
        ? $entry->elements->firstWhere('handle', $heroImage->handle)?->getElementValue()
        : null;
@endphp

{{-- Full-bleed hero --}}
@if($heroImageValue)
    <div class="relative w-full h-96 overflow-hidden">
        <img src="{{ $heroImageValue }}" alt="{{ $entry->title }}" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-linear-to-t from-zinc-900/80 to-transparent flex items-end">
            <div class="px-8 pb-8 max-w-7xl mx-auto w-full">
                <flux:heading size="xl" class="text-white!">{{ $entry->title }}</flux:heading>
            </div>
        </div>
    </div>
@endif

<x-themes.wrapper :theme="$theme">
    @if(!$heroImageValue)
        <flux:heading size="xl" class="text-white! mb-6">{{ $entry->title }}</flux:heading>
    @endif

    <flux:text class="mb-8 text-zinc-400!">
        @if($entry->author) {{ $entry->author->name }} @endif
        @if($entry->published_at) • {{ $entry->published_at->format('F j, Y') }} @endif
    </flux:text>

    <div class="space-y-6 max-w-7xl">
        @foreach($fields as $field)
            @if($field->type === 'image') @continue @endif
            @php $value = $entry->elements->firstWhere('handle', $field->handle)?->getElementValue(); @endphp
            @if($value !== null && $value !== '')
                <x-dynamic-component
                    :component="'field-renderers.' . $field->type"
                    :field="$field"
                    :value="$value"
                />
            @endif
        @endforeach
    </div>

    @foreach($entry->getPageBuilderSections() as $section)
        <x-dynamic-component
            :component="'sections.' . str_replace('_', '-', $section['type'])"
            :section="$section"
            :assets="collect()"
            wire:key="section-{{ $section['_id'] }}"
        />
    @endforeach
</x-themes.wrapper>
```

- [ ] **Step 3: Create `minimal.blade.php`**

```blade
@props(['entry', 'theme' => 'greenpeace'])
@php
    $fields = $entry->blueprint->tabs->sortBy('sort_order')
        ->flatMap(fn($tab) => $tab->sections->sortBy('sort_order')
            ->flatMap(fn($section) => $section->fields->sortBy('order')))
        ->reject(fn($field) => $field->type === 'page_builder');
@endphp

<x-themes.wrapper :theme="$theme">
    <div class="mx-auto max-w-2xl">
        <flux:heading size="xl" class="text-white! mb-2">{{ $entry->title }}</flux:heading>
        <flux:text class="mb-8 text-zinc-400!">
            @if($entry->author) {{ $entry->author->name }} @endif
            @if($entry->published_at) • {{ $entry->published_at->format('F j, Y') }} @endif
        </flux:text>

        <flux:separator class="mb-8" />

        <dl class="space-y-6">
            @foreach($fields as $field)
                @php $value = $entry->elements->firstWhere('handle', $field->handle)?->getElementValue(); @endphp
                @if($value !== null && $value !== '')
                    <div>
                        <flux:text class="text-sm font-semibold text-zinc-500 dark:text-zinc-400 mb-1">{{ $field->label }}</flux:text>
                        <x-dynamic-component
                            :component="'field-renderers.' . $field->type"
                            :field="$field"
                            :value="$value"
                        />
                    </div>
                @endif
            @endforeach
        </dl>
    </div>
</x-themes.wrapper>
```

- [ ] **Step 4: Commit**

```bash
git add resources/views/components/templates/
git commit -m "feat: add article, full-width, and minimal detail templates"
```

---

## Task 7: `CollectionIndex` generic Livewire component + tests

**Files:**
- Create: `app/Livewire/Frontend/CollectionIndex.php`
- Create: `resources/views/livewire/frontend/collection-index.blade.php`
- Create: `tests/Feature/Frontend/CollectionIndexTest.php`

- [ ] **Step 1: Write the failing tests**

```php
<?php

// tests/Feature/Frontend/CollectionIndexTest.php

use App\Models\Blueprint;
use App\Models\Collection;
use App\Models\Entry;

test('collection index returns 404 for unknown slug', function () {
    $this->get('/nonexistent-collection')->assertNotFound();
});

test('collection index renders published entries', function () {
    $blueprint = Blueprint::factory()->create();
    $collection = Collection::factory()->create([
        'slug' => 'my-blog',
        'blueprint_id' => $blueprint->id,
        'is_active' => true,
    ]);

    Entry::factory()->create([
        'blueprint_id' => $blueprint->id,
        'title' => 'Hello World',
        'status' => 'published',
        'published_at' => now(),
    ]);
    Entry::factory()->create([
        'blueprint_id' => $blueprint->id,
        'title' => 'Draft Entry',
        'status' => 'draft',
    ]);

    $this->get('/my-blog')
        ->assertOk()
        ->assertSee('Hello World')
        ->assertDontSee('Draft Entry');
});

test('collection index uses correct template from settings', function () {
    $blueprint = Blueprint::factory()->create();
    $collection = Collection::factory()->create([
        'slug' => 'my-portfolio',
        'blueprint_id' => $blueprint->id,
        'is_active' => true,
        'settings' => ['index_template' => 'list'],
    ]);

    $this->get('/my-portfolio')->assertOk();
});
```

- [ ] **Step 2: Run tests to confirm they fail**

```bash
php artisan test --compact --filter=CollectionIndexTest
```

Expected: FAIL (route not found)

- [ ] **Step 3: Create `CollectionIndex` Livewire component**

```php
<?php

namespace App\Livewire\Frontend;

use App\Models\Collection as CollectionModel;
use App\Support\TemplateLayouts;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
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
        $entries = $this->collection->entries()
            ->where('status', 'published')
            ->with(['elements.Field', 'author'])
            ->latest('published_at')
            ->paginate(9);

        $template = $this->collection->settings['index_template']
            ?? TemplateLayouts::defaultIndexTemplate();

        return view('livewire.frontend.collection-index', [
            'entries'  => $entries,
            'template' => $template,
            'theme'    => $this->collection->settings['theme'] ?? 'greenpeace',
        ]);
    }

    public function title(): string
    {
        return $this->collection->name;
    }
}
```

- [ ] **Step 4: Create the view**

```blade
{{-- resources/views/livewire/frontend/collection-index.blade.php --}}
<div>
    <x-dynamic-component
        :component="'templates.index.' . $template"
        :collection="$collection"
        :entries="$entries"
        :theme="$theme"
    />
</div>
```

- [ ] **Step 5: Add the generic route to `routes/web.php`**

After the `Route::get('/', Home::class)` line and before the `Route::get('dashboard', ...)` route, replace the hardcoded collection routes with:

```php
// Generic collection/entry frontend routes — must come after all specific routes
Route::get('/{collectionSlug}', \App\Livewire\Frontend\CollectionIndex::class)->name('collection.index');
```

Remove the old hardcoded lines:
```php
// DELETE these four lines:
Route::get('/blog', BlogIndex::class)->name('blog.index');
Route::get('/blog/{slug}', BlogShow::class)->name('blog.show');
Route::get('/portfolio', PortfolioIndex::class)->name('portfolio.index');
Route::get('/contact', ContactPage::class)->name('contact');
```

- [ ] **Step 6: Run the tests**

```bash
php artisan test --compact --filter=CollectionIndexTest
```

Expected: PASS

- [ ] **Step 7: Format and commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Livewire/Frontend/CollectionIndex.php \
  resources/views/livewire/frontend/collection-index.blade.php \
  routes/web.php \
  tests/Feature/Frontend/CollectionIndexTest.php
git commit -m "feat: add generic CollectionIndex Livewire component and route"
```

---

## Task 8: `EntryShow` generic Livewire component + tests

**Files:**
- Create: `app/Livewire/Frontend/EntryShow.php`
- Create: `resources/views/livewire/frontend/entry-show.blade.php`
- Create: `tests/Feature/Frontend/EntryShowTest.php`

- [ ] **Step 1: Write the failing tests**

```php
<?php

// tests/Feature/Frontend/EntryShowTest.php

use App\Models\Blueprint;
use App\Models\Collection;
use App\Models\Entry;
use App\Models\Field;

test('entry show returns 404 for unknown collection', function () {
    $this->get('/nonexistent/some-entry')->assertNotFound();
});

test('entry show returns 404 for draft entry', function () {
    $blueprint = Blueprint::factory()->create();
    Collection::factory()->create([
        'slug' => 'my-blog',
        'blueprint_id' => $blueprint->id,
        'is_active' => true,
    ]);
    Entry::factory()->create([
        'blueprint_id' => $blueprint->id,
        'slug' => 'draft-post',
        'status' => 'draft',
    ]);

    $this->get('/my-blog/draft-post')->assertNotFound();
});

test('entry show renders published entry with field values', function () {
    $blueprint = Blueprint::factory()->create();
    Collection::factory()->create([
        'slug' => 'my-blog',
        'blueprint_id' => $blueprint->id,
        'is_active' => true,
    ]);
    $entry = Entry::factory()->create([
        'blueprint_id' => $blueprint->id,
        'title' => 'My Test Post',
        'slug' => 'my-test-post',
        'status' => 'published',
        'published_at' => now(),
    ]);

    $field = Field::factory()->create([
        'blueprint_id' => $blueprint->id,
        'type' => 'textarea',
        'handle' => 'excerpt',
        'label' => 'Excerpt',
    ]);

    $entry->elements()->create([
        'field_id' => $field->id,
        'handle' => 'excerpt',
        'value' => 'This is the excerpt.',
    ]);

    $this->get('/my-blog/my-test-post')
        ->assertOk()
        ->assertSee('My Test Post')
        ->assertSee('This is the excerpt.');
});
```

- [ ] **Step 2: Run tests to confirm they fail**

```bash
php artisan test --compact --filter=EntryShowTest
```

Expected: FAIL (route not found)

- [ ] **Step 3: Create `EntryShow` Livewire component**

```php
<?php

namespace App\Livewire\Frontend;

use App\Models\Collection as CollectionModel;
use App\Models\Entry;
use App\Support\TemplateLayouts;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

class EntryShow extends Component
{
    public Entry $entry;

    public CollectionModel $collection;

    public function mount(string $collectionSlug, string $entrySlug): void
    {
        $this->collection = CollectionModel::query()
            ->where('slug', $collectionSlug)
            ->where('is_active', true)
            ->firstOrFail();

        $this->entry = Entry::query()
            ->where('slug', $entrySlug)
            ->where('blueprint_id', $this->collection->blueprint_id)
            ->where('status', 'published')
            ->with(['elements.Field', 'blueprint.tabs.sections.fields.children', 'author'])
            ->firstOrFail();
    }

    #[Layout('components.layouts.frontend')]
    public function render(): View|Factory
    {
        $template = $this->entry->blueprint->settings['detail_template']
            ?? TemplateLayouts::defaultDetailTemplate();

        return view('livewire.frontend.entry-show', [
            'template' => $template,
            'theme'    => $this->collection->settings['theme'] ?? 'greenpeace',
        ]);
    }

    public function title(): string
    {
        return $this->entry->title;
    }
}
```

- [ ] **Step 4: Create the view**

```blade
{{-- resources/views/livewire/frontend/entry-show.blade.php --}}
<div>
    <x-dynamic-component
        :component="'templates.detail.' . $template"
        :entry="$entry"
        :theme="$theme"
    />
</div>
```

- [ ] **Step 5: Add the entry show route to `routes/web.php`**

After the `collection.index` route added in Task 7:

```php
Route::get('/{collectionSlug}/{entrySlug}', \App\Livewire\Frontend\EntryShow::class)->name('entry.show');
```

- [ ] **Step 6: Run the tests**

```bash
php artisan test --compact --filter=EntryShowTest
```

Expected: PASS

- [ ] **Step 7: Format and commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Livewire/Frontend/EntryShow.php \
  resources/views/livewire/frontend/entry-show.blade.php \
  routes/web.php \
  tests/Feature/Frontend/EntryShowTest.php
git commit -m "feat: add generic EntryShow Livewire component and route"
```

---

## Task 9: Remove old hardcoded components, clean up routes

**Files:**
- Delete: 4 old Livewire classes + 4 views
- Modify: `routes/web.php` — remove leftover imports

- [ ] **Step 1: Delete the old frontend files**

```bash
rm app/Livewire/Frontend/BlogIndex.php
rm app/Livewire/Frontend/BlogShow.php
rm app/Livewire/Frontend/PortfolioIndex.php
rm app/Livewire/Frontend/ContactPage.php
rm resources/views/livewire/frontend/blog-index.blade.php
rm resources/views/livewire/frontend/blog-show.blade.php
rm resources/views/livewire/frontend/portfolio-index.blade.php
rm resources/views/livewire/frontend/contact-page.blade.php
```

- [ ] **Step 2: Clean up `routes/web.php` imports**

Remove these `use` statements from the top of `routes/web.php`:

```php
// DELETE these lines:
use App\Livewire\Frontend\BlogIndex;
use App\Livewire\Frontend\BlogShow;
use App\Livewire\Frontend\ContactPage;
use App\Livewire\Frontend\PortfolioIndex;
```

- [ ] **Step 3: Run the full test suite**

```bash
php artisan test --compact
```

Expected: All tests pass. If any test references the deleted components (e.g. `HomeSectionsTest`), verify it still passes since `Home` was not removed.

- [ ] **Step 4: Run pint**

```bash
vendor/bin/pint --dirty --format agent
```

- [ ] **Step 5: Commit**

```bash
git add -A
git commit -m "chore: remove hardcoded BlogIndex, BlogShow, PortfolioIndex, ContactPage frontend components"
```

---

## Task 10: Seed existing collections with default templates

**Files:**
- No new files — run via artisan tinker

Existing `blog` and `portfolio` collections should get `index_template = 'card-grid'`. Existing blog-post-type blueprints should get `detail_template = 'article'`.

- [ ] **Step 1: Set templates on existing collections**

```bash
php artisan tinker --execute '
\App\Models\Collection::whereIn("slug", ["blog", "portfolio"])->each(function ($c) {
    $settings = $c->settings ?? [];
    $settings["index_template"] = "card-grid";
    $c->update(["settings" => $settings]);
    echo "Updated collection: " . $c->slug . PHP_EOL;
});
'
```

- [ ] **Step 2: Set templates on existing blueprints**

```bash
php artisan tinker --execute '
\App\Models\Blueprint::all()->each(function ($b) {
    $settings = $b->settings ?? [];
    $settings["detail_template"] = "article";
    $b->update(["settings" => $settings]);
    echo "Updated blueprint: " . $b->name . PHP_EOL;
});
'
```

- [ ] **Step 3: Verify the blog still works**

Visit `/blog` and `/blog/{any-published-slug}` in the browser. Both should render using `CollectionIndex` + `card-grid` and `EntryShow` + `article` templates respectively.

- [ ] **Step 4: Commit**

```bash
git commit --allow-empty -m "chore: seed default templates on existing collections and blueprints"
```

(Empty commit is fine — the changes were made via tinker, not code.)
