<?php

namespace App\Livewire\Actions;

use App\Models\Activity;
use App\Models\Blueprint;
use App\Models\Entry;
use Illuminate\Support\Facades\DB;

class CreateEntry
{
    public function execute(array $data): Entry
    {
        return DB::transaction(function () use ($data) {
            // Create the entry
            $entry = \App\Models\Entry::query()->create([
                'collection_id' => $data['collection_id'],
                'blueprint_id' => $data['blueprint_id'],
                'author_id' => auth()->id(),
                'title' => $data['title'],
                'slug' => $data['slug'],
                'status' => $data['status'],
                'published_at' => $data['published_at'] ?? null,
            ]);

            // Create entry elements from field values
            $this->syncEntryElements($entry, $data['fieldValues'] ?? []);

            // Log activity
            \App\Models\Activity::query()->create([
                'log_name' => 'entry',
                'description' => 'Created entry',
                'subject_type' => Entry::class,
                'subject_id' => $entry->id,
                'causer_type' => \App\Models\User::class,
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
