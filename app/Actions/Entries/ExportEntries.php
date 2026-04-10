<?php

namespace App\Actions\Entries;

use App\Models\Entry;

class ExportEntries
{
    public function handle(?int $collectionId = null, bool $includeBlueprint = true): string
    {
        $query = Entry::query()
            ->with(['elements', 'blueprint.fields', 'collection'])
            ->when($collectionId, fn ($q) => $q->whereHas('collection', fn ($c) => $c->where('id', $collectionId)));

        $entries = $query->get()->map(function (Entry $entry) use ($includeBlueprint): array {
            $fields = [];
            foreach ($entry->elements as $element) {
                if (! isset($element->meta['parent_handle'])) {
                    $fields[$element->handle] = $element->getElementValue();
                }
            }

            $data = [
                'title' => $entry->title,
                'slug' => $entry->slug,
                'status' => $entry->status,
                'published_at' => $entry->published_at?->toIso8601String(),
                'seo_title' => $entry->seo_title,
                'seo_description' => $entry->seo_description,
                'og_image' => $entry->og_image,
                'collection' => $entry->collection?->slug,
                'fields' => $fields,
            ];

            if ($includeBlueprint) {
                $data['blueprint'] = $entry->blueprint?->slug;
            }

            return $data;
        });

        return json_encode([
            'exported_at' => now()->toIso8601String(),
            'count' => $entries->count(),
            'entries' => $entries->all(),
        ], JSON_PRETTY_PRINT);
    }
}
