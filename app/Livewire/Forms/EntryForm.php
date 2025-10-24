<?php

namespace App\Livewire\Forms;

use App\Models\Blueprint;
use App\Models\Collection;
use App\Models\Entry;
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

    protected function blueprint(): ?Blueprint
    {
        return $this->blueprint_id !== null && $this->blueprint_id !== 0
            ? Blueprint::with('elements')->find($this->blueprint_id)
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
        $this->collection_id = $entry->collection_id;
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

    public function setCollection(Collection $collection): void
    {
        $this->collection_id = $collection->id;
        $this->blueprint_id = $collection->blueprint_id;
        $this->initializeFieldValues();
    }

    public function initializeFieldValues(): void
    {
        $bp = $this->blueprint();
        if (! $bp instanceof \App\Models\Blueprint) {
            return;
        }

        foreach ($bp->elements as $el) {
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
        $this->fieldValues[$handle]['items'][] = []; // empty block; UI fills it in
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
            'collection_id' => ['required', 'exists:collections,id'],
            'blueprint_id' => ['required', 'exists:blueprints,id'],
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('entries', 'slug')->ignore($this->entry?->id)],
            'status' => ['required', Rule::in(['draft', 'published', 'archived'])],
            'published_at' => ['nullable', 'date'],
        ];

        $bp = $this->blueprint();
        if (! $bp instanceof \App\Models\Blueprint) {
            return $rules;
        }

        foreach ($bp->elements as $el) {
            $h = $el->handle;
            if ($el->type !== 'repeater') {
                $rules["fieldValues.$h"] = $this->rulesForSimple($el->type, $el->is_required, $el->config ?? []);

                continue;
            }

            // repeater container
            $min = $el->config['min'] ?? 0;
            $max = $el->config['max'] ?? null;

            $arr = ['array', "min:$min"];
            if ($max) {
                $arr[] = "max:$max";
            }
            $rules["fieldValues.$h.items"] = $arr;

            // children
            foreach (($el->config['blueprint'] ?? []) as $child) {
                $nh = $child['handle'];
                $childReq = $child['is_required'] ?? false;
                $rules["fieldValues.$h.items.*.$nh"] = $this->rulesForSimple(
                    $child['type'],
                    $childReq,
                    $child['config'] ?? []
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
            'datetime' => ['date'],
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

        $entry = app(\App\Livewire\Actions\CreateEntry::class)->create([
            'collection_id' => $this->collection_id,
            'blueprint_id' => $this->blueprint_id,
            'title' => $this->title,
            'slug' => $this->slug,
            'status' => $this->status,
            'published_at' => $this->published_at,
            'fieldValues' => $this->fieldValues, // includes repeater arrays
        ]);

        Flux::toast(heading: 'Entry Created', text: 'Entry created successfully.', variant: 'success');

        return $entry;
    }

    public function update(int $entryId): Entry
    {
        $this->validate();

        $entry = app(\App\Livewire\Actions\UpdateEntry::class)->update([
            'id' => $entryId,
            'title' => $this->title,
            'slug' => $this->slug,
            'status' => $this->status,
            'published_at' => $this->published_at,
            'fieldValues' => $this->fieldValues,
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
        if (! $bp instanceof \App\Models\Blueprint) {
            return $attrs;
        }

        foreach ($bp->elements as $el) {
            $attrs["fieldValues.{$el->handle}"] = $el->label;
            if ($el->type === 'repeater') {
                foreach (($el->config['blueprint'] ?? []) as $child) {
                    $attrs["fieldValues.{$el->handle}.items.*.{$child['handle']}"] =
                        "{$el->label} → {$child['label']}";
                }
            }
        }

        return $attrs;
    }
}
