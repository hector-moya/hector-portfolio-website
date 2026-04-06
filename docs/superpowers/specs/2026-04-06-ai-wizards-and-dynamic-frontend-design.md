# AI Wizards & Dynamic Frontend Templates — Design Spec

**Date:** 2026-04-06
**Status:** Approved for implementation

---

## Overview

Two quality-of-life features for the CMS:

1. **AI Wizards** — a Blueprint Wizard and an Entry Wizard powered by `laravel/ai` to speed up content structure and content creation.
2. **Dynamic Frontend Template System** — generic index and detail page rendering driven by template selection on collections and blueprints, replacing hardcoded Livewire frontend components.

Both features use Flux UI components wherever possible.

---

## Feature 1: Blueprint AI Wizard

### Purpose
Speed up the time-consuming process of designing a blueprint (tabs → sections → fields) by letting the AI propose a structure from a plain-English description.

### Entry Point
A **"Create with AI"** button on `/blueprints` (index page), alongside the existing "Create Blueprint" button.

### Route
`/blueprints/ai-wizard` — full-page Livewire component (not a modal, needs space for the review tree).

### Step Flow

| Step | Name | Description |
|------|------|-------------|
| 1 | Describe | Free-text input: "What is this blueprint for?" |
| 2 | AI Generates | `laravel/ai` produces structured JSON. Loading state shown (~2–4s). |
| 3 | Review | Read-only expandable tree of proposed tabs → sections → fields. User can remove items but not edit inline. |
| 4 | Confirm & Save | Blueprint + all tabs/sections/fields created in DB. Redirects to standard `/blueprints/{id}/edit`. |

### AI Output Format

The AI is prompted with a system message explaining the blueprint schema and instructed to return strict JSON:

```json
{
  "name": "Blog Post",
  "slug": "blog-post",
  "description": "Standard blog post blueprint",
  "tabs": [
    {
      "name": "Content",
      "sort_order": 1,
      "sections": [
        {
          "name": "Hero",
          "sort_order": 1,
          "fields": [
            { "type": "image", "label": "Featured Image", "handle": "featured_image", "is_required": false, "instructions": "" },
            { "type": "textarea", "label": "Excerpt", "handle": "excerpt", "is_required": false, "instructions": "" }
          ]
        }
      ]
    },
    {
      "name": "SEO",
      "sort_order": 2,
      "sections": [
        {
          "name": "SEO Fields",
          "sort_order": 1,
          "fields": [
            { "type": "text", "label": "SEO Title", "handle": "seo_title", "is_required": false, "instructions": "" },
            { "type": "textarea", "label": "SEO Description", "handle": "seo_description", "is_required": false, "instructions": "" }
          ]
        }
      ]
    }
  ]
}
```

All field handles are slugified and unique. `FieldType` enum values are used for `type`.

### Livewire Component
**`app/Livewire/Blueprints/AiWizard.php`**

Properties:
- `$step` (1–4)
- `$description` (string)
- `$proposal` (array — decoded AI JSON)
- `$loading` (bool)

Actions:
- `generate()` — calls `laravel/ai`, sets `$proposal`
- `removeTab(int $index)`, `removeSection(int $tabIndex, int $sectionIndex)`, `removeField(...)`
- `save()` — creates Blueprint, Tabs, Sections, Fields in DB, redirects

### View
**`resources/views/livewire/blueprints/ai-wizard.blade.php`**

Uses Flux UI: `<flux:card>`, `<flux:input>`, `<flux:button>`, `<flux:badge>`, `<flux:heading>`, `<flux:separator>`. Step indicator built with Flux badges.

---

## Feature 2: Entry AI Wizard

### Purpose
Generate a fully pre-filled draft entry (all text fields + SEO) from a topic description, aware of the blueprint structure already defined.

### Entry Point
A **"Create with AI"** button on the entries index page `/entries` and on any collection-scoped entries page.

### Route
`/entries/ai-wizard` — full-page Livewire component.

### Step Flow

| Step | Name | Description |
|------|------|-------------|
| 1 | Pick Collection | Dropdown of active collections. Blueprint is inferred from the selected collection. |
| 2 | Describe Topic | Free-text: "What is this entry about?" — title + brief. |
| 3 | AI Generates | AI receives full blueprint field schema + topic brief. Fills text fields, scaffolds media. Loading state (~3–6s). |
| 4 | Review & Save | All generated field values rendered as editable inputs (reuses existing field components). Saved as `draft`. Redirects to `/entries/{id}/edit`. |

### What the AI fills vs. scaffolds

**Fills with real content:**
- `text`, `textarea` → generated copy
- `richtext` → full HTML content
- `select`, `radio` → best-fit option chosen from available options
- `toggle` → sensible boolean default
- `number` → reasonable value
- `url`, `email` → placeholder values
- `seo_title`, `seo_description` → generated from topic
- `date` → today's date

**Scaffolds (structure only, no media):**
- `image`, `file` → `null` (upload slot ready)
- `repeater` → N empty rows with text sub-fields pre-filled, media sub-fields empty
- `page_builder` → skipped entirely
- `calendar`, `time` → left empty

### Entry Status
Always saved as `draft`. Never auto-published.

### Livewire Component
**`app/Livewire/Entries/AiWizard.php`**

Properties:
- `$step` (1–4)
- `$collection_id` (int)
- `$description` (string)
- `$generatedFields` (array — handle → value map)
- `$loading` (bool)

Actions:
- `updatedCollectionId()` — loads blueprint + fields
- `generate()` — calls `laravel/ai` with field schema context
- `save()` — creates Entry + EntryElements, redirects

### View
**`resources/views/livewire/entries/ai-wizard.blade.php`**

Review step (step 4) reuses the existing entry field partial components so field rendering is consistent with the standard edit page. Uses Flux UI throughout.

---

## Feature 3: Dynamic Frontend Template System

### Overview

Replace the four hardcoded frontend Livewire components (`BlogIndex`, `BlogShow`, `PortfolioIndex`, `ContactPage`) with two generic components:

- **`CollectionIndex`** — renders any collection's entry listing using the collection's `index_template`
- **`EntryShow`** — renders any entry using the blueprint's `detail_template`

### Data Model Changes

**`collections.settings` (existing JSON column) — add key:**
```json
{ "index_template": "card-grid" }
```

**`blueprints` — new `settings` JSON column (migration required):**
```json
{ "detail_template": "article" }
```

**Migration:** `add_settings_to_blueprints_table`
```php
$table->json('settings')->nullable();
```

### Routing

Existing collection-specific routes are replaced with generic slug-based routes:

```php
// index: /blog, /portfolio, /contact, etc.
Route::get('/{collection:slug}', CollectionIndex::class)->name('collection.index');

// detail: /blog/my-post, /portfolio/my-project, etc.
Route::get('/{collection:slug}/{entry:slug}', EntryShow::class)->name('entry.show');
```

These routes are added after all specific routes to avoid conflicts.

### Generic Livewire Components

**`app/Livewire/Frontend/CollectionIndex.php`**
- Loads collection by slug, paginates published entries
- Reads `$collection->settings['index_template']` (defaults to `card-grid`)
- Passes collection + entries to the chosen index template

**`app/Livewire/Frontend/EntryShow.php`**
- Loads entry by slug within the collection
- Loads blueprint, eager-loads elements
- Reads `$entry->blueprint->settings['detail_template']` (defaults to `article`)
- Passes entry + fields to the chosen detail template

### Index Templates

Located at `resources/views/components/templates/index/`:

| Template | File | Description |
|----------|------|-------------|
| `card-grid` | `card-grid.blade.php` | 3-col responsive grid. Title, excerpt, date per card. Best for blogs, portfolio. |
| `list` | `list.blade.php` | Vertical rows with thumbnail + meta. Best for news, changelogs. |
| `magazine` | `magazine.blade.php` | Featured first entry hero + smaller cards below. Best for editorial content. |

Each index template receives `$collection` and `$entries` (paginated). Entry cards link to `route('entry.show', [$collection, $entry])`.

### Detail Templates

Located at `resources/views/components/templates/detail/`:

| Template | File | Description |
|----------|------|-------------|
| `article` | `article.blade.php` | Centered `max-w-3xl` prose. First image field rendered at top. Best for blog posts. |
| `full-width` | `full-width.blade.php` | Edge-to-edge hero image, then `max-w-7xl` content. Best for portfolio, landing pages. |
| `minimal` | `minimal.blade.php` | Label + value rows, no extra chrome. Best for contact pages, simple records. |

Each detail template receives `$entry` (with elements eager-loaded) and loops through fields using the field renderer.

### Field Type Renderer

Located at `resources/views/components/field-renderers/`:

| Field Type | Component | Output |
|------------|-----------|--------|
| `text`, `textarea` | `text.blade.php` | `<p>` or `<span>` |
| `richtext` | `richtext.blade.php` | `<div class="prose prose-lg dark:prose-invert">` |
| `image` | `image.blade.php` | `<img>` with alt text |
| `url` | `url.blade.php` | `<a>` link (opens in new tab) |
| `email` | `email.blade.php` | `<a href="mailto:...">` |
| `date` | `date.blade.php` | Formatted date string |
| `toggle` | `toggle.blade.php` | Flux `<flux:badge>` — Yes/No |
| `select`, `radio` | `select.blade.php` | Flux `<flux:badge>` with value label |
| `number` | `number.blade.php` | Plain number output |
| `file` | `file.blade.php` | Download link with filename |
| `repeater` | `repeater.blade.php` | Nested loop, recurses through sub-fields |
| `page_builder` | — | Skipped (handled by existing `getPageBuilderSections()`) |

Detail templates call: `<x-field-renderers.{type} :field="$field" :value="$value" />`

### Template Selection in Admin

**Collection create/edit form** — new `Index Template` `<flux:select>` dropdown added to `CollectionForm`. Options: card-grid, list, magazine. Stored in `settings.index_template`.

**Blueprint create/edit form** — new `Detail Template` `<flux:select>` dropdown added to `BlueprintForm`. Options: article, full-width, minimal. Stored in `settings.detail_template`.

### Migration of Existing Frontend Components

The following components are **deleted** and replaced by the generic system:

| Old Component | Replacement |
|---------------|-------------|
| `app/Livewire/Frontend/BlogIndex.php` + view | `CollectionIndex` + `card-grid` template |
| `app/Livewire/Frontend/BlogShow.php` + view | `EntryShow` + `article` template |
| `app/Livewire/Frontend/PortfolioIndex.php` + view | `CollectionIndex` + `card-grid` template |
| `app/Livewire/Frontend/ContactPage.php` + view | `EntryShow` + `minimal` template |

Existing collection records for `blog` and `portfolio` get `settings.index_template = "card-grid"` seeded via migration or tinker. Existing blueprints for blog posts get `settings.detail_template = "article"`.

---

## Flux UI Usage

Both wizards and all frontend templates use Flux UI components:

- `<flux:card>` — wizard steps, template containers
- `<flux:input>`, `<flux:textarea>` — description inputs, review step editable fields
- `<flux:select>` — collection picker, template dropdowns in admin
- `<flux:button variant="primary">` — primary actions
- `<flux:badge>` — field type labels, toggle/select/radio rendered values, step indicators
- `<flux:heading>` — section titles
- `<flux:separator>` — between wizard steps
- `<flux:text>` — body copy in templates

Custom CSS is avoided — Tailwind utilities are used only where no Flux component covers the need.

---

## Files to Create

### AI Wizards
- `app/Livewire/Blueprints/AiWizard.php`
- `resources/views/livewire/blueprints/ai-wizard.blade.php`
- `app/Livewire/Entries/AiWizard.php`
- `resources/views/livewire/entries/ai-wizard.blade.php`

### Frontend Templates
- `database/migrations/xxxx_add_settings_to_blueprints_table.php`
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

### Files to Modify
- `app/Livewire/Forms/CollectionForm.php` — add `index_template`
- `app/Livewire/Forms/BlueprintForm.php` — add `detail_template`
- `resources/views/livewire/collections/create.blade.php` + `edit.blade.php` — add template dropdown
- `resources/views/livewire/blueprints/create.blade.php` + `edit.blade.php` — add template dropdown
- `routes/web.php` — add generic collection/entry routes

### Files to Delete
- `app/Livewire/Frontend/BlogIndex.php`
- `app/Livewire/Frontend/BlogShow.php`
- `app/Livewire/Frontend/PortfolioIndex.php`
- `app/Livewire/Frontend/ContactPage.php`
- `resources/views/livewire/frontend/blog-index.blade.php`
- `resources/views/livewire/frontend/blog-show.blade.php`
- `resources/views/livewire/frontend/portfolio-index.blade.php`
- `resources/views/livewire/frontend/contact-page.blade.php`

---

## Testing

- Feature tests for `CollectionIndex` and `EntryShow` covering: missing collection (404), missing entry (404), correct template selection, fallback to defaults.
- Feature tests for both AI wizards covering: step navigation, save with valid proposal, redirect after save.
- AI generation calls are mocked in tests — no live API calls.
