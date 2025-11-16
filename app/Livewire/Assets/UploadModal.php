<?php

namespace App\Livewire\Assets;

use App\Livewire\Forms\AssetForm;
use App\Models\Asset;
use Flux\Flux;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class UploadModal extends Component
{
    use WithFileUploads;

    public AssetForm $form;

    public $showCreateFolderModal = false;

    public $currentFolder = '/';

    public $searchQuery = '';

    public $upload;

    public $selectedAsset;

    public $newFolderName = '';

    public ?int $currentFolderId = null;

    public function mount(): void
    {
        $this->authorize('viewAny', Asset::class);
    }

    public function uploadAsset(): void
    {
        foreach ($this->form->uploadedFiles as $file) {
            $this->form->upload = $file;
            $this->prepareAssetAttributes();
            $this->form->create();
        }

        $this->dispatch('asset-uploaded');
    }

    #[On('folder-changed')]
    public function onFolderChanged(?int $folderId): void
    {
        $this->currentFolderId = $folderId;
    }

    private function prepareAssetAttributes(): void
    {
        $disk = config('filesystems.default');

        // For S3 in staging/production
        $path = $disk === 's3' ? $this->form->upload->storePublicly($this->currentFolder, 's3') : $this->form->upload->storePublicly($this->currentFolder, 'public');

        $this->form->filename = basename((string) $path);
        $this->form->original_filename = $this->form->upload->getClientOriginalName();
        $this->form->disk = $disk;
        $this->form->mime_type = $this->form->upload->getMimeType();
        $this->form->size = $this->form->upload->getSize();
        $this->form->path = $path;
        $this->form->folder_id = $this->currentFolderId;
        $this->form->uploaded_by = auth()->id();
        $this->form->updated_by = auth()->id();
    }

    public function selectAsset($assetId = null): void
    {
        if ($assetId) {
            $this->selectedAsset = Asset::query()->findOrFail($assetId);
            $this->dispatch('asset-selected', $this->selectedAsset->id);
            Flux::modal('upload-files')->close();
        }
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

    #[Computed]
    public function assets(): LengthAwarePaginator
    {
        return Asset::query()
            ->when($this->searchQuery, function ($query): void {
                $query->where(function ($q): void {
                    $q->where('filename', 'like', "%{$this->searchQuery}%")
                        ->orWhere('original_filename', 'like', "%{$this->searchQuery}%");
                });
            })
            ->when($this->currentFolderId, fn ($query) => $query->where('folder_id', $this->currentFolderId))
            ->latest()
            ->paginate(24);
    }

    public function render(): View|Factory
    {

        return view('livewire.assets.upload-modal');
    }
}
