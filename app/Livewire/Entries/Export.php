<?php

namespace App\Livewire\Entries;

use App\Models\Collection;
use App\Models\Entry;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;

// TODO: Extract the export() method's query-build and serialization logic into an ExportEntries action
//       (app/Actions/Entries/ExportEntries.php). The action should accept optional $collectionId and
//       $includeBlueprint parameters and return the serialized JSON payload (or a Collection of arrays).
//       The component's export() should call the action and stream the result, keeping the component
//       responsible only for streaming the response — not for building the data shape.
//       This also makes the export logic testable in isolation without needing a Livewire test context.
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
