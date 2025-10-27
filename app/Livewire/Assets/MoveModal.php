<?php

namespace App\Livewire\Assets;

use App\Livewire\Forms\AssetForm;
use App\Livewire\Forms\FolderForm;
use App\Models\Asset;
use App\Models\Folder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class MoveModal extends Component
{
    use WithPagination;

    public AssetForm $form;

    public FolderForm $folderForm;

    public ?string $targetFolder = null;

    public array $currentFolderPath = [];

    public ?int $assetId = null;

    public ?string $search = '';

    public ?string $filter = null;

    public $sortBy = 'display_name';

    public $sortDirection = 'asc';

    public ?int $currentFolderId = null;

    public array $assetIds = [];

    public array $selected = [];

    public function mount(): void
    {
        // $this->form->setAsset($this->assetId);
        $this->assetIds = $this->selected;
        $this->currentFolderPath = $this->buildCurrentPath();
    }

    public function buildCurrentPath(): array
    {
        return explode('/', trim($this->form->folder ?? '', '/'));
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
            ])
            ->with('updater')
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
            ])
            ->with('updater')
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

        if ($this->sortBy && $this->sortBy !== '0') {
            $combinedQuery->orderBy($this->sortBy, $this->sortDirection);
        } else {
            // Default sorting: folders first, then by name
            $combinedQuery->orderByRaw("type = 'folder' DESC, display_name ASC");
        }

        return $combinedQuery->get();
    }

    public function enter(int $folderId): void
    {
        $this->currentFolderId = $folderId;
        $this->resetPage();
    }

    #[Computed]
    public function breadcrumbs(): array
    {
        return $this->folderForm->breadcrumbs();
    }

    public function openFolder(?int $id): void
    {
        $this->folderForm->currentFolderId = $id;
        $this->resetPage();
        $this->dispatch('folder-changed', $id);
    }

    public function up(): void
    {
        if ($this->currentFolderId === null || $this->currentFolderId === 0) {
            return;
        }
        $parentId = \App\Models\Folder::query()->whereKey($this->currentFolderId)->value('parent_id');
        $this->currentFolderId = $parentId; // may become null (root)
        $this->resetPage();
    }

    // public function move(): void
    // {
    //     $this->form->move($this->form->assetId, $this->targetFolder);

    //     $this->dispatch('asset-moved');
    // }

    public function move(): void
    {
        $this->authorize('update', Asset::class); // or per-asset check

        DB::transaction(function (): void {
            \App\Models\Asset::query()->whereIn('id', $this->assetIds)->update([
                'folder_id' => $this->currentFolderId,
                // optionally update `path` if you mirror storage folders
            ]);
        });

        $this->dispatch('assets-moved', count: count($this->assetIds));
        \Flux\Flux::modal('move-asset')->close();
        $this->assetIds = [];
    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.assets.move-modal');
    }
}
