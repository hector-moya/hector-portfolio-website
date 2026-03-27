<?php

namespace App\Livewire\Assets;

use App\Actions\Assets\MoveAsset;
use App\Livewire\Forms\AssetForm;
use App\Livewire\Forms\FolderForm;
use App\Models\Asset;
use App\Models\Folder;
use Flux\Flux;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Index extends Component
{
    use WithPagination;

    public AssetForm $form;

    public FolderForm $folderForm;

    public ?string $search = '';

    public ?string $folder = null;

    public string $sortBy = 'display_name';

    public string $sortDirection = 'asc';

    public ?string $filter = null;

    public ?int $assetToMoveId = null;

    public array $selected = [];

    public array $assetIds = [];

    public int $uploadModalKey = 1;

    public function mount(): void
    {
        $this->authorize('viewAny', Asset::class);
    }

    public function download(int $assetId): StreamedResponse
    {
        $this->dispatch('download-file');

        return $this->form->download($assetId);
    }

    public function openMoveAssetModal(?int $assetId): void
    {
        $this->assetIds = $assetId !== null && $assetId !== 0 ? [$assetId] : $this->selected;
        Flux::modal('move-asset')->show();
    }

    public function openNewFolderModal(): void
    {
        $this->folderForm->reset('name', 'parent_id');
        Flux::modal('new-folder')->show();
    }

    public function openRenameFolderModal(?int $folderId): void
    {
        $this->folderForm->set($folderId);
        Flux::modal('rename-folder')->show();
    }

    public function renameFolder(int $folderId): void
    {
        $this->folderForm->update($folderId);
        Flux::modal('rename-folder')->close();

        $this->dispatch('folder-changed', $this->folderForm->currentFolderId);
    }

    #[On('assets-moved')]
    public function onAssetsMoved(): void
    {
        $this->selected = [];
        $this->assetIds = [];
        $this->folderForm->currentFolderId = null;
        Flux::modal('move-asset')->close();
        $this->resetPage();
    }

    #[On('asset-uploaded')]
    public function onAssetUploaded(): void
    {
        $this->uploadModalKey++;
        Flux::modal('upload-files')->close();
        $this->resetPage();
    }

    public function openAsset(int $assetId): void
    {
        $this->dispatch('open-asset-edit', assetId: $assetId);
    }

    public function dropAssetOnFolder(int $assetId, int $folderId): void
    {
        $this->authorize('update', [auth()->user(), Asset::class]);

        $folder = Folder::query()->findOrFail($folderId);

        app(MoveAsset::class)->move(
            assetId: $assetId,
            targetFolder: trim((string) $folder->path, '/'),
            folderId: $folderId,
        );

        unset($this->items);

        Flux::toast(
            heading: 'Asset Moved',
            text: "Moved to {$folder->name}.",
            variant: 'success',
        );
    }

    public function delete(int $assetId): void
    {
        $this->form->destroy($assetId);

        $this->dispatch('asset-deleted');
    }

    public function deleteFolder(int $folderId): void
    {
        $this->folderForm->destroy($folderId);

        $this->dispatch('folder-changed', $this->folderForm->currentFolderId);
    }

    #[On('asset-deleted')]
    public function onAssetDeleted(): void
    {
        $this->resetPage();
    }

    #[On('asset-updated')]
    public function onAssetUpdated(): void
    {
        unset($this->items);
    }

    #[Computed]
    public function items(): Collection
    {
        // Folders query
        $folders = Folder::query()
            ->select([
                'id',
                'name as display_name',
                'updated_at',
                'updated_by',
                DB::raw('"folder" as type'),
                DB::raw('NULL as mime_type'),
                DB::raw('NULL as path'),
                DB::raw('NULL as size'),
                DB::raw('(SELECT COUNT(*) FROM assets WHERE assets.folder_id = folders.id AND assets.deleted_at IS NULL) as assets_count'),
            ])
            ->with(['updater'])
            ->where('parent_id', $this->folderForm->currentFolderId)
            ->when($this->search, fn ($query) => $query->where('name', 'like', "%{$this->search}%")
            );

        // Assets query
        $assets = Asset::query()
            ->select([
                'id',
                'original_filename as display_name',
                'updated_at',
                'updated_by',
                DB::raw('"asset" as type'),
                'mime_type',
                'path',
                'size',
                DB::raw('NULL as assets_count'),
            ])
            ->with(['updater'])
            ->where('folder_id', $this->folderForm->currentFolderId)
            ->when($this->search, fn ($query) => $query->where('original_filename', 'like', "%{$this->search}%")
            )
            ->when($this->filter, fn ($query) => match ($this->filter) {
                'images' => $query->where('mime_type', 'like', 'image/%'),
                'documents' => $query->whereIn('mime_type', [
                    'application/pdf',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                ]),
                default => $query,
            });

        // Combine queries and paginate
        $combinedQuery = $folders->union($assets);

        if ($this->sortBy !== '' && $this->sortBy !== '0') {
            $combinedQuery->orderBy($this->sortBy, $this->sortDirection);
        } else {
            // Default sorting: folders first, then by name
            $combinedQuery->orderByRaw("type = 'folder' DESC, display_name ASC");
        }

        return $combinedQuery->get();
    }

    #[Computed]
    public function breadcrumbs(): array
    {
        return $this->folderForm->breadcrumbs();
    }

    public function sort($column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function openFolder(?int $id): void
    {
        $this->folderForm->currentFolderId = $id;
        $this->resetPage();
        $this->dispatch('folder-changed', $id);
    }

    public function createFolder(): void
    {
        $this->folderForm->create();

        Flux::modal('new-folder')->close();

        $this->dispatch('folder-changed', $this->folderForm->currentFolderId);
    }

    #[Title('Assets')]
    public function render(): View|Factory
    {

        return view('livewire.assets.index');
    }
}
