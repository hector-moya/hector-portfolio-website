<?php

namespace App\Livewire\Entries;

use App\Models\Collection;
use App\Models\Entry;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Export extends Component
{
    public ?int $collectionId = null;

    public bool $includeBlueprint = true;

    #[Computed]
    public function collections(): \Illuminate\Database\Eloquent\Collection
    {
        return Collection::query()->orderBy('name')->get();
    }

    public function export(): StreamedResponse
    {
        $query = Entry::query()
            ->with(['elements', 'blueprint.fields', 'collection'])
            ->when($this->collectionId, fn ($q) => $q->whereHas('collection', fn ($c) => $c->where('id', $this->collectionId)));

        $entries = $query->get()->map(function (Entry $entry): array {
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

            if ($this->includeBlueprint) {
                $data['blueprint'] = $entry->blueprint?->slug;
            }

            return $data;
        });

        $payload = json_encode([
            'exported_at' => now()->toIso8601String(),
            'count' => $entries->count(),
            'entries' => $entries->toArray(),
        ], JSON_PRETTY_PRINT);

        $filename = 'entries-export-'.now()->format('Y-m-d').'.json';

        return response()->streamDownload(function () use ($payload): void {
            echo $payload;
        }, $filename, ['Content-Type' => 'application/json']);
    }

    public function render(): View|Factory
    {
        return view('livewire.entries.export');
    }
}
