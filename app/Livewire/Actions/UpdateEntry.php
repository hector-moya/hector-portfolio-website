<?php

namespace App\Livewire\Actions;

use App\Models\Activity;
use App\Models\Entry;
use App\Models\EntryElement;
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
                'seo_title' => $entryData['seo_title'] ?? null,
                'seo_description' => $entryData['seo_description'] ?? null,
                'og_image' => $entryData['og_image'] ?? null,
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
        // Get existing elements grouped by field_id
        $existingElements = $entry->elements->groupBy('field_id');
        $processedFieldIds = [];
        $processedRepeaterHandles = [];

        foreach ($fieldsValues as $fieldData) {
            $fieldId = $fieldData['field_id'];
            $handle = $fieldData['handle'];
            $type = $fieldData['type'];
            $value = $fieldData['value'];
            $children = $fieldData['children'] ?? [];

            // Handle repeater fields - create individual elements for each child
            if ($type === 'repeater') {
                $processedRepeaterHandles[] = $handle;

                // Delete all existing elements that belong to this repeater
                $entry->elements()
                    ->whereJsonContains('meta->parent_handle', $handle)
                    ->delete();

                // Create new elements for each child field in each item
                $items = $value['items'] ?? [];
                foreach ($items as $index => $itemData) {
                    foreach ($children as $childField) {
                        $childHandle = $childField['handle'];
                        $childValue = $itemData[$childHandle] ?? null;

                        $processedFieldIds[] = $childField['id'];

                        /** @var EntryElement $element */
                        $element = $entry->elements()->create([
                            'field_id' => $childField['id'],
                            'handle' => $childHandle,
                            'meta' => [
                                'parent_handle' => $handle,
                                'index' => $index,
                            ],
                        ]);

                        $element->setElementValue($childValue);
                        $element->save();
                    }
                }

                continue;
            }

            $processedFieldIds[] = $fieldId;

            // Handle regular fields - update or create
            /** @var EntryElement|null $existingElement */
            $existingElement = $existingElements->get($fieldId)?->first();

            if ($existingElement) {
                $existingElement->setElementValue($value);
                $existingElement->save();
            } else {
                /** @var EntryElement $element */
                $element = $entry->elements()->create([
                    'field_id' => $fieldId,
                    'handle' => $handle,
                ]);
                $element->setElementValue($value);
                $element->save();
            }
        }

        // Remove elements that no longer exist in fieldsValues (excluding repeater children which were already handled)
        $entry->elements()
            ->whereNotIn('field_id', $processedFieldIds)
            ->whereNull('meta->parent_handle')
            ->delete();
    }
}
