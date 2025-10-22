<?php

namespace App\Livewire\Actions;

use App\Models\Activity;
use App\Models\Blueprint;
use App\Models\Entry;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UpdateEntry
{
    public function update(array $entryData): Entry
    {
        return DB::transaction(function () use ($entryData) {
            $entry = \App\Models\Entry::query()->findOrFail($entryData['id']);

            // Store old values for logging
            $oldValues = [
                'title' => $entry->title,
                'slug' => $entry->slug,
                'status' => $entry->status,
            ];

            // Update the entry
            $entry->update([
                'title' => $entryData['title'],
                'slug' => $entryData['slug'],
                'status' => $entryData['status'],
                'published_at' => $entryData['published_at'] ?? null,
            ]);

            // Sync entry elements
            $this->syncEntryElements($entry, $entryData['fieldValues'] ?? []);

            // Log activity
            Activity::query()->create([
                'log_name' => 'entry',
                'description' => 'Updated entry',
                'subject_type' => Entry::class,
                'subject_id' => $entry->id,
                'causer_type' => User::class,
                'causer_id' => auth()->id(),
                'event' => 'updated',
                'properties' => [
                    'old' => $oldValues,
                    'new' => [
                        'title' => $entry->title,
                        'slug' => $entry->slug,
                        'status' => $entry->status,
                    ],
                ],
            ]);

            return $entry->fresh(['elements', 'collection', 'blueprint']);
        });
    }

    protected function syncEntryElements(Entry $entry, array $fieldValues): void
    {
        $blueprint = Blueprint::with('elements')->find($entry->blueprint_id);

        if (! $blueprint) {
            return;
        }

        // Get existing elements indexed by handle
        $existingElements = $entry->elements->keyBy('handle');

        foreach ($blueprint->elements as $element) {
            $value = $fieldValues[$element->handle] ?? $this->getDefaultValue($element->type);
            $sanitizedValue = $this->sanitizeValue($value, $element->type);

            // Update existing or create new
            if ($existingElements->has($element->handle)) {
                $existingElements[$element->handle]->update([
                    'value' => $sanitizedValue,
                ]);
            } else {
                $entry->elements()->create([
                    'blueprint_element_id' => $element->id,
                    'handle' => $element->handle,
                    'value' => $sanitizedValue,
                ]);
            }
        }

        // Remove elements that no longer exist in blueprint
        $blueprintHandles = $blueprint->elements->pluck('handle')->toArray();
        $entry->elements()->whereNotIn('handle', $blueprintHandles)->delete();
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
