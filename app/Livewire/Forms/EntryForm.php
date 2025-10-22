<?php

namespace App\Livewire\Forms;

use App\Livewire\Actions\CreateEntry;
use App\Livewire\Actions\UpdateEntry;
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
            $this->fieldValues[$element->handle] = $element->value;
        }
    }

    public function setCollection(Collection $collection): void
    {
        $this->collection_id = $collection->id;
        $this->blueprint_id = $collection->blueprint_id;
        $this->initializeFieldValues();
    }

    public function create(): Entry
    {
        $this->validate();

        $entry = app(CreateEntry::class)->create(
            entryData: [
                'collection_id' => $this->collection_id,
                'blueprint_id' => $this->blueprint_id,
                'title' => $this->title,
                'slug' => $this->slug,
                'status' => $this->status,
                'published_at' => $this->published_at,
                'fieldValues' => $this->fieldValues,
            ]);

        Flux::toast(
            heading: 'Entry Created',
            text: 'Entry created successfully.',
            variant: 'success',
        );

        return $entry;
    }

    public function update(int $entryId): Entry
    {
        $this->validate();

        $entry = app(UpdateEntry::class)->update(
            entryData: [
                'id' => $entryId,
                'title' => $this->title,
                'slug' => $this->slug,
                'status' => $this->status,
                'published_at' => $this->published_at,
                'fieldValues' => $this->fieldValues,
            ]);

        Flux::toast(
            heading: 'Entry Updated',
            text: 'Entry updated successfully.',
            variant: 'success',
        );

        return $entry;
    }

    public function initializeFieldValues(): void
    {
        if ($this->blueprint_id === null || $this->blueprint_id === 0) {
            return;
        }

        $blueprint = Blueprint::with('elements')->find($this->blueprint_id);

        if (! $blueprint) {
            return;
        }

        foreach ($blueprint->elements as $element) {
            if (! isset($this->fieldValues[$element->handle])) {
                $this->fieldValues[$element->handle] = $this->getDefaultValue($element->type);
            }
        }
    }

    protected function getDefaultValue(string $type): mixed
    {
        return match ($type) {
            'checkbox' => false,
            'number' => null,
            'select', 'radio' => '',
            default => '',
        };
    }

    public function rules(): array
    {
        $rules = [
            'collection_id' => ['required', 'exists:collections,id'],
            'blueprint_id' => ['required', 'exists:blueprints,id'],
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('entries', 'slug')->ignore($this->entry?->id),
            ],
            'status' => ['required', Rule::in(['draft', 'published', 'archived'])],
            'published_at' => ['nullable', 'date'],
        ];

        // Add dynamic field validation rules
        if ($this->blueprint_id !== null && $this->blueprint_id !== 0) {
            $blueprint = Blueprint::with('elements')->find($this->blueprint_id);

            if ($blueprint) {
                foreach ($blueprint->elements as $element) {
                    $fieldRules = [];

                    $fieldRules[] = $element->is_required ? 'required' : 'nullable';

                    // Add type-specific validation
                    $fieldRules = array_merge($fieldRules, $this->getFieldTypeRules($element->type));

                    $rules["fieldValues.{$element->handle}"] = $fieldRules;
                }
            }
        }

        return $rules;
    }

    protected function getFieldTypeRules(string $type): array
    {
        return match ($type) {
            'text' => ['string', 'max:255'],
            'textarea', 'richtext' => ['string'],
            'email' => ['email', 'max:255'],
            'url' => ['url', 'max:255'],
            'number' => ['numeric'],
            'date' => ['date'],
            'time' => ['date_format:H:i'],
            'datetime' => ['date'],
            'checkbox' => ['boolean'],
            'select', 'radio' => ['string'],
            'image' => ['string', 'max:255'], // Will store file path
            'file' => ['string', 'max:255'], // Will store file path
            default => ['string'],
        };
    }

    public function validationAttributes(): array
    {
        $attributes = [
            'collection_id' => 'collection',
            'blueprint_id' => 'blueprint',
            'title' => 'title',
            'slug' => 'slug',
            'status' => 'status',
            'published_at' => 'publish date',
        ];

        // Add friendly names for dynamic fields
        if ($this->blueprint_id !== null && $this->blueprint_id !== 0) {
            $blueprint = Blueprint::with('elements')->find($this->blueprint_id);

            if ($blueprint) {
                foreach ($blueprint->elements as $element) {
                    $attributes["fieldValues.{$element->handle}"] = $element->label;
                }
            }
        }

        return $attributes;
    }
}
