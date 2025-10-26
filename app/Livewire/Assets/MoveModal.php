<?php

namespace App\Livewire\Assets;

use Livewire\Component;
use App\Livewire\Forms\AssetForm;
use Livewire\WithPagination;
use App\Models\Asset;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;

class MoveModal extends Component
{
    use WithPagination;
    public AssetForm $form;
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
            ->with('updater')
            ->where('parent_id', $this->currentFolderId)
            ->orderBy('name')
            ->paginate(10);
    }
    public function enter(int $folderId): void
    {
        $this->currentFolderId = $folderId;
        $this->resetPage();
    }

    public function up(): void
    {
        if (! $this->currentFolderId)
            return;
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
