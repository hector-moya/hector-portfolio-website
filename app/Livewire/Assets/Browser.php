<?php

declare(strict_types=1);

namespace App\Livewire\Assets;

use App\Models\Asset;
use App\Models\Folder;
use App\Services\ImageTransformer;
use Flux\Flux;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

final class Browser extends Component
{
    use WithFileUploads;
    use WithPagination;

    public string $fieldHandle;

    public ?string $search = '';

    /** 'image' or 'file' — controls which MIME types are shown */
    public string $mode = 'image';

    public ?int $currentFolderId = null;

    /** @var array<int, mixed> Files staged for upload */
    public array $pendingUploads = [];

    public int $lastUploadCount = 0;

    public function mount(string $fieldHandle, string $mode = 'image'): void
    {
        $this->fieldHandle = $fieldHandle;
        $this->mode = $mode;
    }

    public function selectAsset(int $assetId): void
    {
        $this->dispatch('asset-selected', handle: $this->fieldHandle, value: $assetId);

        Flux::modal('asset-browser-'.$this->fieldHandle)->close();
    }

    public function openFolder(?int $folderId): void
    {
        $this->currentFolderId = $folderId;
        $this->resetPage();
    }

    public function uploadFiles(): void
    {
        foreach ($this->pendingUploads as $file) {
            $this->processUpload($file);
        }

        $this->lastUploadCount = count($this->pendingUploads);
        $this->pendingUploads = [];
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

    private function processUpload(mixed $file): void
    {
        $disk = config('filesystems.default');
        $folder = '/';

        $path = $disk === 's3'
            ? $file->storePublicly($folder, 's3')
            : $file->storePublicly($folder, 'public');

        $mimeType = $file->getMimeType();
        $meta = null;

        if (str_starts_with((string) $mimeType, 'image/')) {
            $variants = resolve(ImageTransformer::class)->transform((string) $path, (string) $disk);

            if ($variants !== []) {
                $meta = [
                    'thumbnail' => $variants['thumbnail'],
                    'medium' => $variants['medium'],
                ];
            }
        }

        Asset::query()->create([
            'filename' => basename((string) $path),
            'original_filename' => $file->getClientOriginalName(),
            'disk' => $disk,
            'mime_type' => $mimeType,
            'size' => $file->getSize(),
            'path' => $path,
            'folder_id' => $this->currentFolderId,
            'uploaded_by' => auth()->id(),
            'updated_by' => auth()->id(),
            'meta' => $meta,
        ]);
    }
}
