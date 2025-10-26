<?php

namespace App\Livewire\Assets;

use App\Livewire\Forms\AssetForm;
use App\Livewire\Forms\FolderForm;
use App\Models\Asset;
use App\Models\Folder;
use Flux\Flux;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public AssetForm $form;

    public FolderForm $folderForm;

    public ?string $search = '';

    public ?string $folder = null;

    public string $sortBy = 'date';

    public string $sortDirection = 'desc';

    public ?string $filter = null;

    public ?int $assetToMoveId = null;

    public array $selected = [];

    public int $uploadModalKey = 1;

    public function mount(): void
    {
        $this->authorize('viewAny', Asset::class);
    }

    public function download(int $assetId): Response
    {
        return $this->form->download($assetId);
    }

    public function openMoveAssetModal(?int $assetId): void
    {
        $this->selected = $assetId ? [$assetId] : $this->selected;
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

        $this->dispatch('folder-changed');
    }

    #[On('asset-moved')]
    public function onAssetMoved(): void
    {
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

    public function delete(int $assetId): void
    {
        $this->form->destroy($assetId);

        $this->dispatch('asset-deleted');
    }

    public function deleteFolder(int $folderId): void
    {
        $this->folderForm->destroy($folderId);

        $this->dispatch('folder-changed');
    }

    #[On('asset-deleted')]
    public function onAssetDeleted(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function assets(): LengthAwarePaginator
    {
        $query = Asset::query()
            ->when($this->search, fn ($query) => $query->where('original_filename', 'like', "%{$this->search}%"))
            ->where('folder_id', $this->folderForm->currentFolderId)
            ->when($this->filter, fn ($query) => match ($this->filter) {
                'images' => $query->where('mime_type', 'like', 'image/%'),
                'documents' => $query->whereIn('mime_type', ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']),
                default => $query,
            })
            ->tap(fn ($query) => $this->sortBy !== '' && $this->sortBy !== '0' ? $query->orderBy($this->sortBy, $this->sortDirection) : $query);

        return $query->paginate(12);
    }

    #[Computed]
    public function folders(): LengthAwarePaginator
    {
        return Folder::query()
            ->with(['updater'])
            ->when($this->search, fn ($query) => $query->where('name', 'like', "%{$this->search}%"))
            ->where('parent_id', $this->folderForm->currentFolderId)
            ->tap(fn ($query) => $this->sortBy ? $query->orderBy($this->sortBy, $this->sortDirection) : $query)
            ->orderBy('name', 'asc')
            ->paginate(12);
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

        $this->dispatch('folder-changed');
    }

    #[Title('Assets')]
    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {

        return view('livewire.assets.index');
    }
}
