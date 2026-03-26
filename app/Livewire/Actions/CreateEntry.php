<?php

namespace App\Livewire\Actions;

use App\Models\Activity;
use App\Models\Entry;
use App\Models\EntryElement;
use App\Models\User;
use Illuminate\Support\Facades\DB;

// TODO: No authorization check. Add Gate::authorize('create', Entry::class) before the transaction,
//       consistent with CreateTaxonomy, CreateUser, CreateGlobal, and CreateAsset.
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
                'seo_title' => $entryData['seo_title'] ?? null,
                'seo_description' => $entryData['seo_description'] ?? null,
                'og_image' => $entryData['og_image'] ?? null,
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
            $children = $fieldData['children'] ?? [];

            // Handle repeater fields - create individual elements for each child field in each item
            if ($type === 'repeater') {
                $items = $value['items'] ?? [];

                foreach ($items as $index => $itemData) {
                    // Create an entry_element for each child field within this repeater item
                    foreach ($children as $childField) {
                        $childHandle = $childField['handle'];
                        $childValue = $itemData[$childHandle] ?? null;

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

            /** @var EntryElement $element */
            $element = $entry->elements()->create([
                'field_id' => $fieldId,
                'handle' => $handle,
            ]);

            $element->setElementValue($value);
            $element->save();
        }
    }
}
