<?php

declare(strict_types=1);

namespace App\Livewire\Assets;

use App\Actions\Assets\MoveAsset;
use App\Livewire\Forms\FolderForm;
use App\Models\Asset;
use App\Models\Folder;
use Flux\Flux;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

final class MoveModal extends Component
{
    use WithPagination;

    public FolderForm $folderForm;

    public ?string $search = '';

    public ?string $filter = null;

    public $sortBy = 'display_name';

    public $sortDirection = 'asc';

    // public ?int $currentFolderId = null;

    // public array $assetIds = [];

    public array $selected = [];

    public function mount(): void
    {
        // $this->assetIds = $this->selected;
        // dd($this->selected);
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

        if ($this->sortBy) {
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

    public function openFolder(?int $id): void
    {
        $this->folderForm->currentFolderId = $id;
        $this->resetPage();
        $this->dispatch('folder-changed', $id);
    }

    public function move(): void
    {
        $this->authorize('update', [auth()->user(), Asset::class]);

        $folderId = $this->folderForm->currentFolderId ?: null;
        $folder = $folderId ? Folder::query()->findOrFail($folderId) : null;
        $folderPath = $folder ? mb_trim((string) $folder->path, '/') : '';

        foreach ($this->selected as $assetId) {
            resolve(MoveAsset::class)->move(
                assetId: (int) $assetId,
                targetFolder: $folderPath,
                folderId: $folderId,
            );
        }

        $this->selected = [];
        $this->dispatch('assets-moved');
    }

    public function createFolder(): void
    {
        $this->folderForm->create();
        Flux::modal('new-folder-modal')->close();

        $this->dispatch('folder-changed', $this->folderForm->currentFolderId);
    }

    public function newFolderModal(): void
    {
        $this->folderForm->reset('name', 'parent_id');
        Flux::modal('new-folder-modal')->show();
    }

    public function render(): View|Factory
    {
        return view('livewire.assets.move-modal');
    }
}
