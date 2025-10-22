<?php

namespace App\Livewire\Actions;

use App\Models\Activity;
use App\Models\Blueprint;
use App\Models\Entry;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CreateEntry
{
    public function create(array $entryData): Entry
    {
        return DB::transaction(function () use ($entryData) {
            // Create the entry
            $entry = Entry::query()->create([
                'collection_id' => $entryData['collection_id'],
                'blueprint_id' => $entryData['blueprint_id'],
                'author_id' => auth()->id(),
                'title' => $entryData['title'],
                'slug' => $entryData['slug'],
                'status' => $entryData['status'],
                'published_at' => $entryData['published_at'] ?? null,
            ]);

            // Create entry elements from field values
            $this->syncEntryElements($entry, $entryData['fieldValues'] ?? []);

            // Log activity
            Activity::query()->create([
                'log_name' => 'entry',
                'description' => 'Created entry',
                'subject_type' => Entry::class,
                'subject_id' => $entry->id,
                'causer_type' => User::class,
                'causer_id' => auth()->id(),
                'event' => 'created',
                'properties' => [
                    'title' => $entry->title,
                    'status' => $entry->status,
                ],
            ]);

            return $entry->load('elements', 'collection', 'blueprint');
        });
    }

    protected function syncEntryElements(Entry $entry, array $fieldValues): void
    {
        $blueprint = Blueprint::with('elements')->find($entry->blueprint_id);

        if (! $blueprint) {
            return;
        }

        foreach ($blueprint->elements as $element) {
            $value = $fieldValues[$element->handle] ?? $this->getDefaultValue($element->type);

            $entry->elements()->create([
                'blueprint_element_id' => $element->id,
                'handle' => $element->handle,
                'value' => $this->sanitizeValue($value, $element->type),
            ]);
        }
    }

    protected function getDefaultValue(string $type): mixed
    {
        return match ($type) {
            'checkbox' => false,
            'number' => null,
            default => '',
        };
    }

    protected function sanitizeValue(mixed $value, string $type): mixed
    {
        return match ($type) {
            'checkbox' => (bool) $value,
            'number' => $value ? (float) $value : null,
            default => (string) ($value ?? ''),
        };
    }
}
