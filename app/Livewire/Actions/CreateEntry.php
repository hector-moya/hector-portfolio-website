<?php

namespace App\Livewire\Actions;

use App\Models\Activity;
use App\Models\Entry;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CreateEntry
{
    public function handle(array $entryData): Entry
    {
        return DB::transaction(function () use ($entryData) {
            // Create the entry
            $entry = Entry::query()->create([
                'blueprint_id' => $entryData['blueprint_id'],
                'author_id' => auth()->id(),
                'title' => $entryData['title'],
                'slug' => $entryData['slug'],
                'status' => $entryData['status'],
                'published_at' => $entryData['published_at'] ?? null,
            ]);

            // Create entry elements from fields values
            $this->createEntryElements($entry, $entryData['fieldsValues'] ?? []);

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

            return $entry->load('elements', 'blueprint');
        });
    }

    protected function createEntryElements(Entry $entry, array $fieldsValues): void
    {
        foreach ($fieldsValues as $fieldData) {
            $fieldId = $fieldData['field_id'];
            $handle = $fieldData['handle'];
            $type = $fieldData['type'];
            $value = $fieldData['value'];

            // Handle repeater fields - create one element per item
            if ($type === 'repeater') {
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

            // Handle regular fields
            $element = $entry->elements()->create([
                'field_id' => $fieldId,
                'handle' => $handle,
            ]);

            $element->setElementValue($value);
            $element->save();
        }
    }
}
