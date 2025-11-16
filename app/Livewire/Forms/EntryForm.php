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
            $fieldsValues[] = [
                'field_id' => $field->id,
                'handle' => $field->handle,
                'type' => $field->type,
                'value' => $this->fieldValues[$field->handle] ?? $this->defaultForType($field->type, $field->config ?? []),
            ];
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

        // Load field values from entry elements
        foreach ($entry->elements as $element) {
            $this->fieldValues[$element->handle] = $element->getElementValue();
        }

        $this->initializeFieldValues(); // ensure defaults for any new fields
    }

    public function setCollection(ModelsCollection $collection): void
    {
        $this->collection_id = $collection->id;
        $this->blueprint_id = $collection->blueprint_id;
        $this->initializeFieldValues();
    }

    public function initializeFieldValues(): void
    {
        $bp = $this->blueprint();
        if (! $bp instanceof Blueprint) {
            return;
        }

        foreach ($bp->fields as $el) {
            $h = $el->handle;
            if (! array_key_exists($h, $this->fieldValues)) {
                $this->fieldValues[$h] = $this->defaultForType($el->type, $el->config ?? []);
            }
        }
    }

    /* ---------- Repeater actions (entries) ---------- */

    public function addRepeaterItem(string $handle): void
    {
        $this->fieldValues[$handle] ??= ['items' => []];

        $bp = $this->blueprint();
        if ($bp === null) {
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
