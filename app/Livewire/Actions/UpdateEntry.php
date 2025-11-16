<?php

namespace App\Livewire\Actions;

use App\Models\Activity;
use App\Models\Blueprint;
use App\Models\Entry;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UpdateEntry
{
    public function handle(array $entryData): Entry
    {
        return DB::transaction(function () use ($entryData) {
            $entry = Entry::query()->findOrFail($entryData['id']);

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

            return $entry->fresh(['elements', 'blueprint']);
        });
    }

    protected function syncEntryElements(Entry $entry, array $fieldValues): void
    {
        dd($entry->elements, $fieldValues);
        $blueprint = Blueprint::with('fields')->find($entry->blueprint_id);

        if (! $blueprint) {
            return;
        }

        // Get existing elements indexed by handle
        $existingElements = $entry->elements->keyBy('handle');

        foreach ($blueprint->fields as $field) {
            $value = $fieldValues[$field->handle] ?? $this->getDefaultValue($field->type);
            $sanitizedValue = $this->sanitizeValue($value, $field->type);

            // Update existing or create new
            if ($existingElements->has($field->handle)) {
                $existingEl = $existingElements[$field->handle];
                if ($this->shouldStoreInMeta($field->type)) {
                    $existingEl->update([
                        'value' => null,
                        'meta' => $sanitizedValue,
                    ]);
                } else {
                    $existingEl->setElementValue($sanitizedValue);
                    $existingEl->save();
                }
            } elseif ($this->shouldStoreInMeta($field->type)) {
                $entry->elements()->create([
                    'field_id' => $field->id,
                    'handle' => $field->handle,
                    'value' => null,
                    'meta' => $sanitizedValue,
                ]);
            } else {
                /** @var \App\Models\EntryElement $newElement */
                $newElement = $entry->elements()->create([
                    'field_id' => $field->id,
                    'handle' => $field->handle,
                ]);

                $newElement->setElementValue($sanitizedValue);
                $newElement->save();
            }
        }

        // Remove elements that no longer exist in blueprint
        $blueprintHandles = $blueprint->fields->pluck('handle')->toArray();
        $entry->elements()->whereNotIn('handle', $blueprintHandles)->delete();
    }

    protected function getDefaultValue(string $type): mixed
    {
        return match ($type) {
            'checkbox' => false,
            'number' => null,
            'repeater' => ['items' => []],
            default => '',
        };
    }

    protected function sanitizeValue(mixed $value, string $type): mixed
    {
        return match ($type) {
            'checkbox' => (bool) $value,
            'number' => $value ? (float) $value : null,
            'select' => is_array($value) ? $value : (string) ($value ?? ''),
            'repeater' => [
                'items' => $value['items'] ?? [],
            ],
            default => (string) ($value ?? ''),
        };
    }

    protected function shouldStoreInMeta(string $type): bool
    {
        return in_array($type, ['repeater', 'select']);
    }
}
