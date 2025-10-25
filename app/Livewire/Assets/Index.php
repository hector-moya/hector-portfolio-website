<?php

namespace App\Livewire\Assets;


use App\Models\Asset;
use Livewire\Attributes\Title;
use Illuminate\Http\Response;
use Livewire\Component;
use Flux\Flux;
use Livewire\Attributes\On;
use Livewire\Attributes\Computed;
use App\Livewire\Forms\AssetForm;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public AssetForm $form;
    public ?string $search = '';

    public ?string $folder = null;

    public string $sortBy = 'date';

    public string $sortDirection = 'desc';

    public ?string $filter = null;

    public ?int $assetToMove = null;

    public ?string $targetFolder = null;


    public ?int $assetToDelete = null;
    public int $uploadModalKey = 1;

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

    public function download(int $assetId): Response
    {
        return $this->form->download($assetId);
    }

    public function openMoveAssetModal(int $assetId): void
    {
        $this->assetToMove = $assetId;
        Flux::modal('move-asset')->show();
    }

    public function move(): void
    {
        $this->form->move($this->assetToMove, $this->targetFolder);

        $this->dispatch('asset-moved');
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
            ->when($this->folder, fn ($query) => $query->where('folder', $this->folder))
            ->when($this->filter, fn ($query) => match ($this->filter) {
                'images' => $query->where('mime_type', 'like', 'image/%'),
                'documents' => $query->whereIn('mime_type', ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']),
                default => $query,
            })
            ->tap(fn ($query) => $this->sortBy !== '' && $this->sortBy !== '0' ? $query->orderBy($this->sortBy, $this->sortDirection) : $query);

        return $query->paginate(12);
    }

    #[Title('Assets')]
    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {

        return view('livewire.assets.index');
    }
}
