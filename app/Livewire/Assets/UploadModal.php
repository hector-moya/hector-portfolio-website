<?php

namespace App\Livewire\Assets;

use App\Models\Asset;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class UploadModal extends Component
{
    use WithFileUploads;

    public $showModal = false;

    public $showCreateFolderModal = false;

    public $currentFolder = '/';

    public $searchQuery = '';

    public $upload;

    public $selectedAsset;

    public $newFolderName = '';

    public function mount(): void
    {
        $this->authorize('viewAny', Asset::class);
    }

    #[On('open-asset-browser')]
    public function openModal(): void
    {
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->reset(['upload']);
    }

    public function uploadAsset(): void
    {
        $this->validate([
            'upload' => ['required', 'file', 'max:10240'], // 10MB max
        ]);

        $disk = config('filesystems.default');

        // For S3 in staging/production
        if ($disk === 's3') {
            $path = $this->upload->storePublicly($this->currentFolder, 's3');
        } else {
            // For local development
            $path = $this->upload->storePublicly($this->currentFolder, 'public');
        }

        $asset = \App\Models\Asset::query()->create([
            'filename' => basename((string) $path),
            'original_filename' => $this->upload->getClientOriginalName(),
            'disk' => $disk,
            'mime_type' => $this->upload->getMimeType(),
            'size' => $this->upload->getSize(),
            'path' => $path,
            'folder' => $this->currentFolder,
            'uploaded_by' => auth()->id(),
        ]);

        $this->reset('upload');
        $this->dispatch('asset-uploaded', $asset->id);
    }

    public function selectAsset($assetId = null): void
    {
        if ($assetId) {
            $this->selectedAsset = \App\Models\Asset::query()->findOrFail($assetId);
            $this->dispatch('asset-selected', $this->selectedAsset->id);
        }
        $this->closeModal();
    }

    public function createFolder(): void
    {
        $this->validate([
            'newFolderName' => ['required', 'string', 'min:1', 'max:255'],
        ]);

        $folder = trim((string) $this->currentFolder, '/').'/'.Str::slug($this->newFolderName);
        Storage::disk('public')->makeDirectory($folder);
        $this->currentFolder = $folder;
        $this->showCreateFolderModal = false;
        $this->newFolderName = '';
    }

    public function navigateToFolder($folder): void
    {
        $this->currentFolder = $folder;
    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        $assets = Asset::query()
            ->when($this->searchQuery, function ($query): void {
                $query->where(function ($q): void {
                    $q->where('filename', 'like', "%{$this->searchQuery}%")
                        ->orWhere('original_filename', 'like', "%{$this->searchQuery}%");
                });
            })
            ->where('folder', $this->currentFolder)
            ->latest()
            ->paginate(24);

        return view('livewire.assets.upload-modal', [
            'assets' => $assets,
        ]);
    }
}
