<?php

declare(strict_types=1);

namespace App\Livewire\Assets;

use App\Models\Asset;
use App\Models\Folder;
use Flux\Flux;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

final class Browser extends Component
{
    use WithPagination;

    public string $fieldHandle;

    public ?string $search = '';

    /** 'image' or 'file' — controls which MIME types are shown */
    public string $mode = 'image';

    public ?int $currentFolderId = null;

    public function mount(string $fieldHandle, string $mode = 'image'): void
    {
        $this->fieldHandle = $fieldHandle;
        $this->mode = $mode;
    }

    public function selectAsset(int $assetId): void
    {
        $this->dispatch('asset-selected', [
            'handle' => $this->fieldHandle,
            'value' => $assetId,
        ]);

        Flux::modal('asset-browser-'.$this->fieldHandle)->close();
    }

    public function openFolder(?int $folderId): void
    {
        $this->currentFolderId = $folderId;
        $this->resetPage();
    }

    #[Computed]
    public function folders(): Collection
    {
        return Folder::query()
            ->where('parent_id', $this->currentFolderId)
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function breadcrumbs(): array
    {
        if ($this->currentFolderId === null) {
            return [];
        }

        $folder = Folder::query()->find($this->currentFolderId);

        return $folder ? $folder->ancestors() : [];
    }

    public function render(): View|Factory
    {
        return view('livewire.assets.browser', [
            'assets' => $this->assetsQuery(),
        ]);
    }

    /** @return LengthAwarePaginator<Asset> */
    private function assetsQuery(): LengthAwarePaginator
    {
        return Asset::query()
            ->when($this->search, fn ($q) => $q
                ->where('original_filename', 'like', sprintf('%%%s%%', $this->search))
                ->orWhere('mime_type', 'like', sprintf('%%%s%%', $this->search))
            )
            ->when(
                $this->mode === 'image',
                fn ($q) => $q->where('mime_type', 'like', 'image/%'),
                fn ($q) => $q->where('mime_type', 'not like', 'image/%'),
            )
            ->where('folder_id', $this->currentFolderId)
            ->latest()
            ->paginate(12);
    }
}
