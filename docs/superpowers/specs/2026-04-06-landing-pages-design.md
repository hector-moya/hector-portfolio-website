# Landing Pages — Design Spec

**Date:** 2026-04-06
**Status:** Approved for implementation

---

## Overview

Extend the CMS to support single-entry landing pages (home page, about, services, etc.) that render page builder sections with full layout and styling control. Brings `Home.php` in line with the generic template system and adds a Landing Page AI Wizard that creates a collection + blueprint + entry in one flow.

---

## Goals

- Landing pages render at `/{collectionSlug}` using existing page builder section components
- The `/` home route stays dedicated; `Home.php` is refactored to use the same rendering pipeline
- A "Create with AI" button on the collections index generates a complete landing page from a description
- No new migrations required

---

## Data Model

No schema changes. `collections.settings` is already a JSON column.

**New key added to `settings`:**
```json
{ "type": "single" }
```

| Value | Behaviour |
|---|---|
| `standard` (default) | Current behaviour — paginated entry listing |
| `single` | Loads first published entry, renders with detail template directly |

Single-type collections always use the `landing-page` detail template. There is no dropdown for this — it is hardcoded by the wizard and by `CollectionIndex` when rendering single-type collections. The `detail_template` key in settings is not used for single-type.

Existing collections without a `type` key default to `standard` — no backfill needed.

---

## Routing

No route changes.

| URL | Component | Notes |
|---|---|---|
| `/` | `Home::class` | Dedicated, unchanged |
| `/{collectionSlug}` | `CollectionIndex` | Handles standard and single-type |
| `/{collectionSlug}/{entrySlug}` | `EntryShow` | Unchanged |

---

## Feature 1: Single-Type Collection Rendering

### CollectionIndex changes

`app/Livewire/Frontend/CollectionIndex.php` — add a branch at the top of `render()`:

```php
if (($this->collection->settings['type'] ?? 'standard') === 'single') {
    $entry = $this->collection->entries()
        ->where('status', 'published')
        ->with(['elements.field', 'blueprint'])
        ->latest('published_at')
        ->first();

    $sections = $entry?->getPageBuilderSections() ?? [];
    $assets   = $this->resolveAssets($sections);
    $theme    = $this->collection->settings['theme'] ?? 'greenpeace';

    return view('livewire.frontend.collection-index', compact('entry', 'sections', 'assets', 'theme'));
}
```

The existing listing path is unchanged. For single-type, `render()` also passes `$template = 'landing-page'` and `$isSingle = true`.

`resolveAssets(array $sections): \Illuminate\Database\Eloquent\Collection` — private method duplicated from `Home.php::loadLayoutAssets()`. Collects asset IDs from `bg_image`, `image`, and `images` keys across all sections, batch-loads them from the DB. Same implementation in both `CollectionIndex` and `Home` — no shared service needed for this small function.

`resources/views/livewire/frontend/collection-index.blade.php` — add a top-level branch:
```blade
@if($isSingle ?? false)
    <x-dynamic-component
        :component="'templates.detail.' . $template"
        :entry="$entry"
        :sections="$sections"
        :assets="$assets"
        :theme="$theme"
    />
@else
    {{-- existing listing content unchanged --}}
@endif
```

### Home.php refactor

`app/Livewire/Frontend/Home.php` — rename `loadLayoutAssets()` to `resolveAssets()` to match `CollectionIndex`. No other logic changes. The component keeps its existing structure.

`resources/views/livewire/frontend/home.blade.php` — remove the hardcoded fallback field block (`hero_title`, `hero_subtitle`, `content`, `cta_text_primary`, etc.). Keep only the page builder sections loop and the empty state. The view already renders sections via `<x-dynamic-component>` so the change is small.

### New detail template: `landing-page`

**`resources/views/components/templates/detail/landing-page.blade.php`**

```blade
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
            <flux:heading>{{ $entry->title }}</flux:heading>
            <flux:text class="mt-4">{{ __('Content coming soon.') }}</flux:text>
        </div>
    @endforelse
</x-themes.wrapper>
```

Receives: `$entry`, `$sections`, `$assets`, `$theme`.

### TemplateLayouts

`app/Support/TemplateLayouts.php` — add to `detailTemplates()`:
```php
'landing-page' => 'Landing Page',
```

### Collection admin form

`app/Livewire/Forms/CollectionForm.php` — add `$type` property (default `'standard'`). Stored/retrieved via `settings['type']`.

`resources/views/livewire/collections/create.blade.php` + `edit.blade.php` — add a **Collection Type** `<flux:select>` with options Standard / Single Page. When type is Single Page, the Index Template selector is hidden (irrelevant for single-type collections). No Detail Template dropdown is shown for single-type — it is always `landing-page`.

---

## Feature 2: Landing Page AI Wizard

### Route

```php
Route::get('landing-pages/ai-wizard', \App\Livewire\LandingPages\AiWizard::class)
    ->name('landing-pages.ai-wizard');
```

Added inside the auth middleware group, before any parameterised routes.

A **"Create with AI"** button with `icon="sparkles"` is added to the collections index page alongside the existing "Create Collection" button.

### Agent

**`app/Ai/Agents/LandingPageWizardAgent.php`**

Uses `HasStructuredOutput` and `#[UseSmartestModel]`.

`instructions()` — system prompt explaining the 6 available section types (from `SectionTypes::all()`), their fields, and that image fields must always be `null`. Instructs the model to return an ordered array of sections that makes sense for the described page.

`schema()` — returns:
```json
{
  "sections": [
    {
      "_id": "unique-string",
      "type": "hero|text|image_text|gallery|cta|features",
      "data": { ... }
    }
  ]
}
```

Each section's `data` shape matches the corresponding `SectionTypes` field definitions. Image fields (`bg_image`, `image`, `images`) are always `null` or `[]`. The `_id` is a short unique string (AI generates it; wizard also ensures uniqueness before saving).

### Livewire Component

**`app/Livewire/LandingPages/AiWizard.php`**

Properties:
- `$step` (string: `'describe'` | `'review'`)
- `$name` (string)
- `$slug` (string — auto-generated from name via `updatedName()`, editable)
- `$description` (string)
- `$proposal` (array — decoded AI sections)

Actions:
- `updatedName()` — slugifies `$name` into `$slug`
- `generate()` — validates inputs, calls `LandingPageWizardAgent::make()->prompt()`, sets `$proposal`, advances to `'review'`
- `removeSection(int $index)` — removes section from `$proposal`
- `save()` — creates Collection + Blueprint + Entry, redirects to entry edit

`mount()` — `$this->authorize('create', \App\Models\Collection::class)`

`#[Locked]` on `$proposal`.

**`save()` creates in order:**

1. **Collection**
   ```php
   Collection::create([
       'name'      => $this->name,
       'slug'      => $this->slug,
       'is_active' => true,
       'settings'  => ['type' => 'single'],
   ]);
   ```

2. **Blueprint** (one tab → one section → one `page_builder` field)
   ```php
   CreateBlueprint::create([
       'name' => $this->name . ' Blueprint',
       'slug' => $this->slug . '-blueprint',
       'tabs' => [[
           'name'       => 'Content',
           'sort_order' => 1,
           'sections'   => [[
               'name'       => 'Page Builder',
               'sort_order' => 1,
               'fields'     => [[
                   'type'        => 'page_builder',
                   'label'       => 'Page Sections',
                   'handle'      => 'page_sections',
                   'is_required' => false,
                   'instructions' => '',
               ]],
           ]],
       ]],
   ]);
   $collection->update(['blueprint_id' => $blueprint->id]);
   ```

3. **Entry**
   ```php
   CreateEntry::handle([
       'title'        => $this->name,
       'slug'         => $this->slug,
       'collection_id' => $collection->id,
       'blueprint_id'  => $blueprint->id,
       'status'        => 'draft',
       'fieldsValues'  => [[
           'field_id' => $pageBuilderField->id,
           'handle'   => 'page_sections',
           'type'     => 'page_builder',
           'value'    => $this->proposal,
           'children' => [],
       ]],
   ]);
   ```

Redirects to `route('entries.edit', $entry)`.

### View

**`resources/views/livewire/landing-pages/ai-wizard.blade.php`**

Two-step Flux UI wizard matching the existing wizard pattern.

**Step: describe**
- `flux:input` for Page Name (with `wire:model.live="name"`)
- `flux:input` for Slug (with `wire:model="slug"`, auto-filled but editable)
- `flux:textarea` for Description (rows=5, placeholder describing a landing page)
- Generate button with `wire:loading` state

**Step: review**
- `flux:callout` info banner: "Review the proposed sections. You can remove any you don't want. Images can be added after saving."
- List of proposed sections — each shows a type badge + title preview (from `data.title` or `data.content` truncated)
- Remove button per section
- Back button + "Save as Draft" button with `wire:loading` state

Step indicator: 2 badges — "1 Describe" and "2 Review & Save".

---

## Files to Create

- `app/Ai/Agents/LandingPageWizardAgent.php`
- `app/Livewire/LandingPages/AiWizard.php`
- `resources/views/livewire/landing-pages/ai-wizard.blade.php`
- `resources/views/components/templates/detail/landing-page.blade.php`

## Files to Modify

- `app/Livewire/Frontend/CollectionIndex.php` — single-type branch + `resolveAssets()`
- `app/Livewire/Frontend/Home.php` — align asset resolution with `CollectionIndex`
- `resources/views/livewire/frontend/home.blade.php` — remove hardcoded fallback block
- `app/Support/TemplateLayouts.php` — add `landing-page`
- `app/Livewire/Forms/CollectionForm.php` — add `$type`
- `resources/views/livewire/collections/create.blade.php` — add Type dropdown
- `resources/views/livewire/collections/edit.blade.php` — add Type dropdown
- `resources/views/livewire/collections/index.blade.php` — add "Create with AI" button
- `routes/web.php` — add wizard route

---

## Testing

- Feature tests for `CollectionIndex` single-type: loads first published entry, renders landing-page template, returns 404 for inactive collection
- Feature test for `Home.php`: renders page builder sections, renders empty state when no entry
- Feature tests for `LandingPageAiWizardTest`: page loads, generate sets proposal, remove section, save creates collection + blueprint + entry as draft, viewer denied
- AI agent calls mocked via `LandingPageWizardAgent::fake([...])` — no live API calls
