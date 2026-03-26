<?php

namespace App\Livewire\Entries;

use App\Livewire\Actions\CreateEntry;
use App\Models\Collection;
use App\Models\Field;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

// TODO: Extract the import() method's JSON parsing, collection resolution, and CreateEntry loop into
//       an ImportEntries action (app/Actions/Entries/ImportEntries.php). The action should accept the
//       decoded entries array and return an array with 'imported' and 'skipped' counts.
//       The component should only handle file upload, decode the JSON, call the action, and display results.
//       This mirrors how Export should work and makes the import logic unit-testable without Livewire.
//       Consider also wrapping the entire import loop in a single DB::transaction() inside the action
//       for atomicity — currently a mid-loop failure leaves partial data committed.
class Import extends Component
{
    use WithFileUploads;

    #[Validate('required|file|mimes:json|max:5120')]
    public $file;

    public array $preview = [];

    public bool $imported = false;

    public int $importedCount = 0;

    public int $skippedCount = 0;

    public function updatedFile(): void
    {
        $this->validate(['file' => 'required|file|mimes:json|max:5120']);

        $contents = file_get_contents($this->file->getRealPath());
        $data = json_decode((string) $contents, true);

        $this->preview = array_slice($data['entries'] ?? [], 0, 5);
    }

    public function import(): void
    {
        $this->validate(['file' => 'required|file|mimes:json|max:5120']);

        $contents = file_get_contents($this->file->getRealPath());
        $data = json_decode((string) $contents, true);
        $entries = $data['entries'] ?? [];

        foreach ($entries as $entryData) {
            $collectionSlug = $entryData['collection'] ?? null;
            if (! $collectionSlug) {
                $this->skippedCount++;

                continue;
            }

            $collection = Collection::query()
                ->with('blueprint.fields')
                ->where('slug', $collectionSlug)
                ->first();

            if (! $collection) {
                $this->skippedCount++;

                continue;
            }

            $blueprint = $collection->blueprint;
            if (! $blueprint) {
                $this->skippedCount++;

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

            $this->importedCount++;
        }

        $this->imported = true;
        $this->dispatch('notify', message: "{$this->importedCount} entries imported, {$this->skippedCount} skipped.");
    }

    public function render(): View|Factory
    {
        return view('livewire.entries.import');
    }
}
