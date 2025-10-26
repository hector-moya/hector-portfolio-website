<?php

namespace App\Livewire\Assets;

use App\Livewire\Forms\AssetForm;
use App\Livewire\Forms\FolderForm;
use App\Models\Asset;
use Illuminate\Pagination\LengthAwarePaginator;
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

    public $sortBy = 'date';

    public $sortDirection = 'desc';

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

    public function sort($column)
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    #[Computed]
    public function folders(): LengthAwarePaginator
    {
        return \App\Models\Folder::query()
            ->with('assets')
            ->with('updater')
            ->where('parent_id', $this->folderForm->currentFolderId)
            ->orderBy('name')
            ->paginate(10);
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
        if (! $this->currentFolderId) {
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

        DB::transaction(function () {
            Asset::whereIn('id', $this->assetIds)->update([
                'folder_id' => $this->currentFolderId,
                // optionally update `path` if you mirror storage folders
            ]);
        });

        $this->dispatch('assets-moved', count: count($this->assetIds));
        \Flux\Flux::modal('move-asset')->close();
        $this->assetIds = [];
    }

    public function render()
    {
        return view('livewire.assets.move-modal');
    }
}
