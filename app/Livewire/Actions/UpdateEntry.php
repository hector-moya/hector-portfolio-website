<?php

declare(strict_types=1);

namespace App\Livewire\Actions;

use App\Models\Activity;
use App\Models\Entry;
use App\Models\EntryElement;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final class UpdateEntry
{
    public function handle(array $entryData): Entry
    {
        return DB::transaction(function () use ($entryData) {
            $entry = Entry::query()->findOrFail($entryData['id']);

            $oldValues = [
                'title' => $entry->title,
                'slug' => $entry->slug,
                'status' => $entry->status,
            ];

            $entry->update([
                'title' => $entryData['title'],
                'slug' => $entryData['slug'],
                'status' => $entryData['status'],
                'published_at' => $entryData['published_at'] ?? null,
                'seo_title' => $entryData['seo_title'] ?? null,
                'seo_description' => $entryData['seo_description'] ?? null,
                'og_image' => $entryData['og_image'] ?? null,
            ]);

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

    private function syncEntryElements(Entry $entry, array $fieldsValues): void
    {
        $existingElements = $entry->elements->groupBy('field_id');
        $processedFieldIds = [];

        foreach ($fieldsValues as $fieldData) {
            $fieldId = $fieldData['field_id'];
            $handle = $fieldData['handle'];
            $value = $fieldData['value'];

            $processedFieldIds[] = $fieldId;

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

        // Remove elements that no longer exist in fieldsValues
        $entry->elements()
            ->whereNotIn('field_id', $processedFieldIds)
            ->delete();
    }
}
