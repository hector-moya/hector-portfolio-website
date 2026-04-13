<?php

declare(strict_types=1);

namespace App\Livewire\Forms;

use App\Enums\SectionType;
use App\Livewire\Actions\CreateEntry;
use App\Livewire\Actions\UpdateEntry;
use App\Models\Blueprint;
use App\Models\Collection as ModelsCollection;
use App\Models\Entry;
use Flux\Flux;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Livewire\Form;

final class EntryForm extends Form
{
    public ?Entry $entry = null;

    public ?int $collection_id = null;

    public ?int $blueprint_id = null;

    #[Validate('required|string|max:255')]
    public string $title = '';

    #[Validate('required|string|max:255')]
    public string $slug = '';

    public string $status = 'draft';

    public ?string $published_at = null;

    public string $seo_title = '';

    public string $seo_description = '';

    public string $og_image = '';

    /** @var array<string, mixed> */
    public array $fieldValues = [];

    /* ---------- Lifecycle ---------- */

    public function setEntry(Entry $entry): void
    {
        $this->entry = $entry;
        $this->blueprint_id = $entry->blueprint_id;
        $this->title = $entry->title;
        $this->slug = $entry->slug;
        $this->status = $entry->status;
        $this->published_at = $entry->published_at?->format('Y-m-d\TH:i');
        $this->seo_title = $entry->seo_title ?? '';
        $this->seo_description = $entry->seo_description ?? '';
        $this->og_image = $entry->og_image ?? '';

        $entry->load('elements.Field');
        $this->loadFieldValuesFromEntry($entry);
        $this->initializeFieldValues();
    }

    public function setCollection(ModelsCollection $collection): void
    {
        $this->collection_id = $collection->id;
        $this->blueprint_id = $collection->blueprint_id;
        $this->initializeFieldValues();
    }

    public function initializeFieldValues(): void
    {
        $blueprint = $this->blueprint();
        if (! $blueprint instanceof Blueprint) {
            return;
        }

        foreach ($blueprint->fields as $field) {
            $handle = $field->handle;
            if (! array_key_exists($handle, $this->fieldValues)) {
                $sectionType = SectionType::tryFrom($field->type);
                $this->fieldValues[$handle] = $sectionType?->defaultData() ?? [];
            }
        }
    }

    /* ---------- Validation ---------- */

    public function rules(): array
    {
        $rules = [
            'blueprint_id' => ['required', 'exists:blueprints,id'],
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('entries', 'slug')->ignore($this->entry?->id)],
            'status' => ['required', Rule::in(['draft', 'published', 'archived'])],
            'published_at' => ['nullable', 'date'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string', 'max:500'],
            'og_image' => ['nullable', 'string', 'max:255'],
        ];

        $bp = $this->blueprint();
        if (! $bp instanceof Blueprint) {
            return $rules;
        }

        foreach ($bp->fields as $field) {
            $rules['fieldValues.'.$field->handle] = ['nullable', 'array'];
        }

        return $rules;
    }

    /* ---------- Persistence ---------- */

    public function create(): Entry
    {
        $this->validate();

        $entry = resolve(CreateEntry::class)->handle([
            'collection_id' => $this->collection_id,
            'blueprint_id' => $this->blueprint_id,
            'title' => $this->title,
            'slug' => $this->slug,
            'status' => $this->status,
            'published_at' => $this->published_at,
            'seo_title' => $this->seo_title ?: null,
            'seo_description' => $this->seo_description ?: null,
            'og_image' => $this->og_image ?: null,
            'fieldsValues' => $this->buildFieldsValuesArray(),
        ]);

        Flux::toast(heading: 'Entry Created', text: 'Entry created successfully.', variant: 'success');

        return $entry;
    }

    public function update(int $entryId): Entry
    {
        $entry = resolve(UpdateEntry::class)->handle([
            'id' => $entryId,
            'title' => $this->title,
            'slug' => $this->slug,
            'status' => $this->status,
            'published_at' => $this->published_at,
            'seo_title' => $this->seo_title ?: null,
            'seo_description' => $this->seo_description ?: null,
            'og_image' => $this->og_image ?: null,
            'fieldsValues' => $this->buildFieldsValuesArray(),
        ]);

        Flux::toast(heading: 'Entry Updated', text: 'Entry updated successfully.', variant: 'success');

        return $entry;
    }

    public function validationAttributes(): array
    {
        $attrs = [
            'collection_id' => 'collection',
            'blueprint_id' => 'blueprint',
            'title' => 'title',
            'slug' => 'slug',
            'status' => 'status',
            'published_at' => 'publish date',
        ];

        $bp = $this->blueprint();
        if (! $bp instanceof Blueprint) {
            return $attrs;
        }

        foreach ($bp->fields as $field) {
            $attrs['fieldValues.'.$field->handle] = $field->label;
        }

        return $attrs;
    }

    /* ---------- Shared helpers ---------- */

    private function buildFieldsValuesArray(): array
    {
        $bp = $this->blueprint();
        if (! $bp instanceof Blueprint) {
            return [];
        }

        $fieldsValues = [];

        foreach ($bp->fields as $field) {
            $fieldsValues[] = [
                'field_id' => $field->id,
                'handle' => $field->handle,
                'type' => $field->type,
                'value' => $this->fieldValues[$field->handle] ?? [],
            ];
        }

        return $fieldsValues;
    }

    private function blueprint(): ?Blueprint
    {
        return $this->blueprint_id !== null && $this->blueprint_id !== 0
            ? Blueprint::with('fields')->find($this->blueprint_id)
            : null;
    }

    private function loadFieldValuesFromEntry(Entry $entry): void
    {
        $blueprint = $this->blueprint();
        if (! $blueprint instanceof Blueprint) {
            return;
        }

        foreach ($blueprint->fields as $field) {
            $handle = $field->handle;

            // Find the entry element for this field
            $element = $entry->elements->firstWhere('handle', $handle);
            if ($element !== null) {
                $this->fieldValues[$handle] = $element->getElementValue() ?? [];
            }
        }
    }
}
