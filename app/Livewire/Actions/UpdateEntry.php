<?php

namespace App\Livewire\Actions;

use App\Models\Activity;
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
            $this->syncEntryElements($entry, $entryData['fieldsValues'] ?? []);

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

    protected function syncEntryElements(Entry $entry, array $fieldsValues): void
    {
        // Get existing elements grouped by field_id and handle
        $existingElements = $entry->elements->groupBy('field_id');
        $processedFieldIds = [];

        foreach ($fieldsValues as $fieldData) {
            $fieldId = $fieldData['field_id'];
            $handle = $fieldData['handle'];
            $type = $fieldData['type'];
            $value = $fieldData['value'];

            $processedFieldIds[] = $fieldId;

            // Handle repeater fields - one element per item
            if ($type === 'repeater') {
                // Delete existing repeater items for this field
                $entry->elements()->where('field_id', $fieldId)->delete();

                // Create new items
                $items = $value['items'] ?? [];
                foreach ($items as $index => $itemData) {
                    $entry->elements()->create([
                        'field_id' => $fieldId,
                        'handle' => $handle,
                        'value' => null,
                        'meta' => [
                            'index' => $index,
                            'data' => $itemData,
                        ],
                    ]);
                }

                continue;
            }

            // Handle regular fields - update or create
            $existingElement = $existingElements->get($fieldId)?->first();

            if ($existingElement) {
                $existingElement->setElementValue($value);
                $existingElement->save();
            } else {
                $element = $entry->elements()->create([
                    'field_id' => $fieldId,
                    'handle' => $handle,
                ]);
                $element->setElementValue($value);
                $element->save();
            }
        }

        // Remove elements that no longer exist in fieldsValues
        $entry->elements()->whereNotIn('field_id', $processedFieldIds)->delete();
    }
}
