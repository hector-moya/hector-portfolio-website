<?php

namespace App\Livewire\Assets;

use App\Models\Asset;
use Flux\Flux;
use Livewire\Component;
use Livewire\WithFileUploads;

class Browser extends Component
{
    use WithFileUploads;

    public string $fieldHandle;

    public array $uploads = [];

    public ?string $search = '';

    public function mount(string $fieldHandle): void
    {
        $this->fieldHandle = $fieldHandle;
    }

    public function selectAsset(int $assetId): void
    {
        // Set the field value in the parent form
        $this->dispatch('asset-selected', [
            'handle' => $this->fieldHandle,
            'value' => $assetId,
        ]);

        // Close the modal
        Flux::modal('asset-browser-'.$this->fieldHandle)->close();
    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.assets.browser', [
            'assets' => Asset::query()
                ->when($this->search, function ($query): void {
                    $query->where('original_filename', 'like', "%{$this->search}%")
                        ->orWhere('mime_type', 'like', "%{$this->search}%");
                })
                ->where('mime_type', 'like', 'image/%')
                ->latest()
                ->paginate(12),
        ]);
    }
}
