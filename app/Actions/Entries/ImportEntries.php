<?php

namespace App\Actions\Entries;

use App\Livewire\Actions\CreateEntry;
use App\Models\Collection;
use App\Models\Field;
use Illuminate\Support\Facades\DB;

class ImportEntries
{
    /**
     * @param  array<int, array<string, mixed>>  $entries
     * @return array{imported: int, skipped: int}
     */
    public function handle(array $entries): array
    {
        $imported = 0;
        $skipped = 0;

        DB::transaction(function () use ($entries, &$imported, &$skipped): void {
            foreach ($entries as $entryData) {
                $collectionSlug = $entryData['collection'] ?? null;
                if (! $collectionSlug) {
                    $skipped++;

                    continue;
                }

                $collection = Collection::query()
                    ->with('blueprint.fields')
                    ->where('slug', $collectionSlug)
                    ->first();

                if (! $collection) {
                    $skipped++;

                    continue;
                }

                $blueprint = $collection->blueprint;
                if (! $blueprint) {
                    $skipped++;

                    continue;
                }

                $fieldsValues = [];
                foreach ($blueprint->fields as $field) {
                    $handle = $field->handle;
                    $fieldsValues[] = [
                        'field_id' => $field->id,
                        'handle' => $handle,
                        'type' => $field->type,
                        'value' => $entryData['fields'][$handle] ?? null,
                        'children' => $field->type === 'repeater'
                            ? $field->children->map(fn (Field $c) => ['id' => $c->id, 'handle' => $c->handle, 'type' => $c->type])->toArray()
                            : [],
                    ];
                }

                app(CreateEntry::class)->handle([
                    'blueprint_id' => $blueprint->id,
                    'title' => $entryData['title'],
                    'slug' => $entryData['slug'],
                    'status' => $entryData['status'] ?? 'draft',
                    'published_at' => $entryData['published_at'] ?? null,
                    'seo_title' => $entryData['seo_title'] ?? null,
                    'seo_description' => $entryData['seo_description'] ?? null,
                    'og_image' => $entryData['og_image'] ?? null,
                    'fieldsValues' => $fieldsValues,
                ]);

                $imported++;
            }
        });

        return ['imported' => $imported, 'skipped' => $skipped];
    }
}
