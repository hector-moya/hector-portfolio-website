<?php

declare(strict_types=1);

namespace App\Livewire\Actions;

use App\Models\Activity;
use App\Models\Entry;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

final class CreateEntry
{
    public function handle(array $entryData): Entry
    {
        Gate::authorize('create', Entry::class);

        return DB::transaction(function () use ($entryData) {
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

            $this->createEntryElements($entry, $entryData['fieldsValues'] ?? []);

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

    private function createEntryElements(Entry $entry, array $fieldsValues): void
    {
        foreach ($fieldsValues as $fieldData) {
            $element = $entry->elements()->create([
                'field_id' => $fieldData['field_id'],
                'handle' => $fieldData['handle'],
            ]);

            // Store section data as the element value (arrays go into meta)
            $element->setElementValue($fieldData['value']);
            $element->save();
        }
    }
}
