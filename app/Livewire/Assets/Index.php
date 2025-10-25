<?php

namespace App\Livewire\Assets;


use App\Models\Asset;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    public ?string $search = '';

    public ?string $folder = null;

    public string $sortField = 'created_at';

    public string $sortDirection = 'desc';

    public ?string $filter = null;

    // Move Modal Properties
    public bool $showMoveModal = false;

    public ?int $assetToMove = null;

    public ?string $targetFolder = null;

    // Delete Confirmation Properties
    public bool $showDeleteModal = false;

    public ?int $assetToDelete = null;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function mount(): void
    {
        $this->authorize('viewAny', Asset::class);
    }

    public function download(int $assetId): void
    {
        $asset = Asset::query()->findOrFail($assetId);
        $this->authorize('view', $asset);

        $path = $asset->path;
        $disk = $asset->disk;

        if (! Storage::disk($disk)->exists($path)) {
            return;
        }

        $this->dispatch('download-file', [
            'path' => Storage::disk($disk)->path($path),
            'name' => $asset->original_filename,
        ]);
    }

    public function confirmMove(int $assetId): void
    {
        $this->assetToMove = $assetId;
        $this->targetFolder = $this->folder;
        $this->showMoveModal = true;
    }

    public function move(): void
    {
        $this->validate([
            'assetToMove' => ['required', 'exists:assets,id'],
            'targetFolder' => ['required', 'string'],
        ]);

        $asset = Asset::query()->findOrFail($this->assetToMove);
        $this->authorize('update', $asset);

        $oldPath = $asset->path;
        $newPath = trim((string) $this->targetFolder, '/').'/'.$asset->filename;

        Storage::disk($asset->disk)->move($oldPath, $newPath);

        $asset->update([
            'path' => $newPath,
            'folder' => $this->targetFolder,
        ]);

        $this->showMoveModal = false;
        $this->assetToMove = null;
        $this->targetFolder = null;
        $this->dispatch('asset-moved');
    }

    public function confirmDelete(int $assetId): void
    {
        $this->assetToDelete = $assetId;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        $this->validate([
            'assetToDelete' => ['required', 'exists:assets,id'],
        ]);

        $asset = Asset::query()->findOrFail($this->assetToDelete);
        $this->authorize('delete', $asset);

        Storage::disk($asset->disk)->delete($asset->path);
        $asset->delete();

        $this->showDeleteModal = false;
        $this->assetToDelete = null;
        $this->dispatch('asset-deleted');
    }

    #[Computed]

    public function assets(): LengthAwarePaginator
    {
        $query = Asset::query()
            ->when($this->search, fn ($q) => $q->where('original_filename', 'like', "%{$this->search}%"))
            ->when($this->folder, fn ($q) => $q->where('folder', $this->folder))
            ->when($this->filter, fn ($q) => match ($this->filter) {
                'images' => $q->where('mime_type', 'like', 'image/%'),
                'documents' => $q->whereIn('mime_type', ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']),
                default => $q,
            })
            ->orderBy($this->sortField, $this->sortDirection);

        return $query->paginate(12);
    }

    #[Title('Assets')]
    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {

        return view('livewire.assets.index');
    }
}
