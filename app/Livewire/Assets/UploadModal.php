<?php

namespace App\Livewire\Assets;

use App\Livewire\Forms\AssetForm;
use App\Livewire\Forms\FolderForm;
use App\Models\Asset;
use App\Services\ImageTransformer;
use Flux\Flux;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class UploadModal extends Component
{
    use WithFileUploads;

    public AssetForm $form;

    public FolderForm $folderForm;

    public $showCreateFolderModal = false;

    public $currentFolder = '/';

    public $searchQuery = '';

    public $upload;

    public $selectedAsset;

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

        $mimeType = $this->form->upload->getMimeType();
        $variants = [];

        if (str_starts_with((string) $mimeType, 'image/')) {
            $variants = resolve(ImageTransformer::class)->transform((string) $path, (string) $disk);
        }

        $this->form->filename = basename((string) $path);
        $this->form->original_filename = $this->form->upload->getClientOriginalName();
        $this->form->disk = $disk;
        $this->form->mime_type = $mimeType;
        $this->form->size = $this->form->upload->getSize();
        $this->form->path = $path;
        $this->form->folder_id = $this->currentFolderId;
        $this->form->uploaded_by = auth()->id();
        $this->form->updated_by = auth()->id();

        if ($variants !== []) {
            $this->form->meta = array_merge($this->form->meta ?? [], [
                'thumbnail' => $variants['thumbnail'],
                'medium' => $variants['medium'],
            ]);
        }
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
        $this->folderForm->currentFolderId = $this->currentFolderId;

        $folder = $this->folderForm->create();

        $this->currentFolderId = $folder->id;
        $this->showCreateFolderModal = false;
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
