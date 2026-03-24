<?php

namespace App\Livewire\Forms;

use App\Livewire\Actions\CreateEntry;
use App\Livewire\Actions\UpdateEntry;
use App\Models\Blueprint;
use App\Models\Collection as ModelsCollection;
use App\Models\Entry;
use App\Models\Field;
use Flux\Flux;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Livewire\Form;

class EntryForm extends Form
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

    public array $fieldValues = [];

    /* ---------- Shared helpers ---------- */

    protected function buildFieldsValuesArray(): array
    {
        $bp = $this->blueprint();
        if (! $bp instanceof Blueprint) {
            return [];
        }

        $fieldsValues = [];

        foreach ($bp->fields as $field) {
            $fieldArray = [
                'field_id' => $field->id,
                'handle' => $field->handle,
                'type' => $field->type,
                'value' => $this->fieldValues[$field->handle] ?? $this->defaultForType($field->type, $field->config ?? []),
            ];

            // Add children metadata for repeater fields
            if ($field->type === 'repeater') {
                $fieldArray['children'] = $field->children->map(fn ($child) => [
                    'id' => $child->id,
                    'handle' => $child->handle,
                    'type' => $child->type,
                ])->toArray();
            }

            $fieldsValues[] = $fieldArray;
        }

        return $fieldsValues;
    }

    protected function blueprint(): ?Blueprint
    {
        return $this->blueprint_id !== null && $this->blueprint_id !== 0
            ? Blueprint::with('fields')->find($this->blueprint_id)
            : null;
    }

    protected function defaultForType(string $type, array $config = []): mixed
    {
        return match ($type) {
            'checkbox' => false,
            'number' => null,
            'select', 'radio', 'email', 'url' => '',
            'repeater' => ['items' => []],
            default => '',
        };
    }

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

        // Load field values from entry elements (with Field relationship for legacy support)
        $entry->load('elements.Field');
        $this->loadFieldValuesFromEntry($entry);

        // Ensure defaults for any new fields that might have been added to the blueprint
        $this->initializeFieldValues();
    }

    public function setCollection(ModelsCollection $collection): void
    {
        $this->collection_id = $collection->id;
        $this->blueprint_id = $collection->blueprint_id;
        $this->initializeFieldValues();
    }

    protected function loadFieldValuesFromEntry(Entry $entry): void
    {
        $blueprint = $this->blueprint();
        if (! $blueprint instanceof Blueprint) {
            return;
        }

        // Group all elements by their parent_handle (null for regular fields)
        $regularElements = [];
        $repeaterElements = [];

        foreach ($entry->elements as $element) {
            $parentHandle = $element->meta['parent_handle'] ?? null;

            // Handle legacy data: if no parent_handle but field has a parent_id, look up the parent
            if ($parentHandle === null && $element->Field && $element->Field->parent_id) {
                $parentField = $blueprint->fields->firstWhere('id', $element->Field->parent_id);
                if ($parentField && $parentField->type === 'repeater') {
                    $parentHandle = $parentField->handle;
                    // For legacy data without index, we'll group them sequentially
                    $index = $element->meta['index'] ?? 0;
                }
            }

            if ($parentHandle === null) {
                // Regular field
                $regularElements[$element->handle] = $element;
            } else {
                // Repeater child field
                $index = $element->meta['index'] ?? 0;
                $repeaterElements[$parentHandle][$index][$element->handle] = $element;
            }
        }

        // Load values for each field in the blueprint
        foreach ($blueprint->fields as $field) {
            $handle = $field->handle;

            if ($field->type === 'repeater') {
                // Reconstruct repeater items from individual elements
                $items = [];

                if (isset($repeaterElements[$handle])) {
                    // Sort by index and rebuild each item
                    ksort($repeaterElements[$handle]);

                    foreach ($repeaterElements[$handle] as $index => $itemElements) {
                        $itemData = [];
                        foreach ($field->children as $childField) {
                            $childHandle = $childField->handle;
                            $itemData[$childHandle] = isset($itemElements[$childHandle])
                                ? $itemElements[$childHandle]->getElementValue()
                                : $this->defaultForType($childField->type, $childField->config ?? []);
                        }
                        $items[] = $itemData;
                    }
                }

                $this->fieldValues[$handle] = ['items' => $items];
            } else {
                // Regular field
                if (isset($regularElements[$handle])) {
                    $this->fieldValues[$handle] = $regularElements[$handle]->getElementValue();
                }
            }
        }
    }

    public function initializeFieldValues(): void
    {
        $blueprint = $this->blueprint();
        if (! $blueprint instanceof Blueprint) {
            return;
        }

        foreach ($blueprint->fields as $element) {
            $handle = $element->handle;
            if (! array_key_exists($handle, $this->fieldValues)) {
                $this->fieldValues[$handle] = $this->defaultForType($element->type, $element->config ?? []);
            }
        }
    }

    /* ---------- Repeater actions (entries) ---------- */

    public function addRepeaterItem(string $handle): void
    {
        $this->fieldValues[$handle] ??= ['items' => []];

        $bp = $this->blueprint();
        if (! $bp instanceof Blueprint) {
            $this->fieldValues[$handle]['items'][] = [];

            return;
        }

        $field = $bp->fields->firstWhere('handle', $handle);
        if (! $field || $field->children->isEmpty()) {
            $this->fieldValues[$handle]['items'][] = [];

            return;
        }

        // Initialize new item with defaults for each child field
        $newItem = [];
        foreach ($field->children as $childField) {
            $newItem[$childField->handle] = $this->defaultForType(
                $childField->type,
                $childField->config ?? []
            );
        }

        $this->fieldValues[$handle]['items'][] = $newItem;
    }

    public function removeRepeaterItem(string $handle, int $index): void
    {
        if (! isset($this->fieldValues[$handle]['items'][$index])) {
            return;
        }
        unset($this->fieldValues[$handle]['items'][$index]);
        $this->fieldValues[$handle]['items'] = array_values($this->fieldValues[$handle]['items']);
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

        foreach ($bp->fields as $el) {
            $h = $el->handle;
            if ($el->type !== 'repeater') {
                $rules["fieldValues.$h"] = $this->rulesForSimple($el->type, $el->is_required, $el->config ?? []);

                continue;
            }

            // Repeater container
            $min = $el->config['min'] ?? 0;
            $max = $el->config['max'] ?? null;

            $arr = ['array', "min:$min"];
            if ($max) {
                $arr[] = "max:$max";
            }
            $rules["fieldValues.$h.items"] = $arr;

            // Children from relationship, not config
            foreach ($el->children as $child) {
                $rules["fieldValues.$h.items.*.{$child->handle}"] = $this->rulesForSimple(
                    $child->type,
                    $child->is_required,
                    $child->config ?? []
                );
            }
        }

        return $rules;
    }

    protected function rulesForSimple(string $type, bool $required, array $config): array
    {
        $base = $required ? ['required'] : ['nullable'];

        return array_merge($base, match ($type) {
            'text' => ['string', 'max:'.($config['max'] ?? 255)],
            'textarea' => ['string'],
            'richtext' => ['string'],
            'email' => ['email', 'max:255'],
            'url' => ['url', 'max:255'],
            'number' => ['numeric'],
            'date' => ['date'],
            'time' => ['date_format:H:i'],
            'calendar' => ['date'],
            'checkbox' => ['boolean'],
            'select', 'radio' => ['string'],
            'image', 'file' => ['string'], // your uploader will refine later
            default => ['string'],
        });
    }

    /* ---------- Persistence ---------- */

    public function create(): Entry
    {
        $this->validate();

        $entry = app(CreateEntry::class)->handle([
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
        $entry = app(UpdateEntry::class)->handle([
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

        foreach ($bp->fields as $el) {
            $attrs["fieldValues.{$el->handle}"] = $el->label;
            if ($el->type === 'repeater') {
                foreach ($el->children as $child) {
                    $attrs["fieldValues.{$el->handle}.items.*.{$child->handle}"] =
                        "{$el->label} → {$child->label}";
                }
            }
        }

        return $attrs;
    }
}
