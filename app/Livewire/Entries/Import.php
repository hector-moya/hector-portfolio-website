<?php

namespace App\Livewire\Entries;

use App\Actions\Entries\ImportEntries;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

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

        $result = app(ImportEntries::class)->handle($data['entries'] ?? []);

        $this->importedCount = $result['imported'];
        $this->skippedCount = $result['skipped'];
        $this->imported = true;

        $this->dispatch('notify', message: "{$this->importedCount} entries imported, {$this->skippedCount} skipped.");
    }

    public function render(): View|Factory
    {
        return view('livewire.entries.import');
    }
}
