<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use App\Models\Asset;
use App\Actions\Assets\CreateAsset;
use App\Actions\Assets\UpdateAsset;
use App\Actions\Assets\DeleteAsset;
use App\Actions\Assets\MoveAsset;
use App\Actions\Assets\DownloadAsset;
use Illuminate\Http\Response;
use Flux\Flux;
use Livewire\Form;

class AssetForm extends Form
{
    #[Validate('required', 'file', 'max:10240')]
    public  $upload = null;

    #[Validate('required', 'array')]
    public array $uploadedFiles = [];

    #[Validate('required|string|max:255')]
    public string $filename = '';

    #[Validate('required|string|max:255')]
    public string $original_filename = '';

    #[Validate('required|string|max:255')]
    public string $disk = 'public';

    #[Validate('required|string|max:255')]
    public string $mime_type = '';

    #[Validate('required|integer|min:0')]
    public ?int $size = null;

    #[Validate('required|string|max:255')]
    public string $path = '';
    #[Validate('nullable|string|max:255')]
    public ?string $alt_text = null;
    #[Validate('nullable|string|max:255')]
    public ?string $title = null;
    #[Validate('nullable|string|max:255')]
    public ?string $folder = null;
    #[Validate('array')]
    public array $meta = [];
    #[Validate('required|integer')]
    public ?int $uploaded_by = null;

    public function setAsset(Asset $asset): void
    {
        $this->filename = $asset->filename;
        $this->original_filename = $asset->original_filename;
        $this->disk = $asset->disk;
        $this->mime_type = $asset->mime_type;
        $this->size = $asset->size;
        $this->path = $asset->path;
        $this->alt_text = $asset->alt_text;
        $this->title = $asset->title;
        $this->folder = $asset->folder;
        $this->meta = $asset->meta;
        $this->uploaded_by = $asset->uploaded_by;
    }

    public function create(): Asset
    {
        $this->validate();

        $asset = app(CreateAsset::class)->create([
            'filename' => $this->filename,
            'original_filename' => $this->original_filename,
            'disk' => $this->disk,
            'mime_type' => $this->mime_type,
            'size' => $this->size,
            'path' => $this->path,
            'alt_text' => $this->alt_text,
            'title' => $this->title,
            'folder' => $this->folder,
            'meta' => $this->meta,
            'uploaded_by' => $this->uploaded_by,
        ]);

        Flux::toast(
            heading: 'Asset Created',
            text: 'The asset has been created successfully.',
            variant: 'success',
        );

        $this->reset('filename', 'original_filename', 'disk', 'mime_type', 'size', 'path', 'alt_text', 'title', 'folder', 'meta', 'uploaded_by');

        return $asset;
    }

    public function update(int $assetId): Asset
    {
        $this->validate();

        $asset = app(UpdateAsset::class)->update([
            'id' => $assetId,
            'filename' => $this->filename,
            'original_filename' => $this->original_filename,
            'disk' => $this->disk,
            'mime_type' => $this->mime_type,
            'size' => $this->size,
            'path' => $this->path,
            'alt_text' => $this->alt_text,
            'title' => $this->title,
            'folder' => $this->folder,
            'meta' => $this->meta,
            'uploaded_by' => $this->uploaded_by,
        ]);

        Flux::toast(
            heading: 'Asset Updated',
            text: 'The asset has been updated successfully.',
            variant: 'success',
        );

        return $asset;
    }

    public function destroy(int $assetId): void
    {
        app(DeleteAsset::class)->delete(
            assetData: [
                'id' => $assetId,
            ]
        );

        Flux::toast(
            heading: 'Asset Deleted',
            text: 'The asset has been deleted successfully.',
            variant: 'success',
        );
    }

    public function move(int $assetId, string $targetFolder): void
    {
        app(MoveAsset::class)->move(
            assetId: $assetId,
            targetFolder: $targetFolder,
        );

        Flux::toast(
            heading: 'Asset Moved',
            text: 'The asset has been moved successfully.',
            variant: 'success',
        );
    }

    public function download(int $assetId): Response
    {
        return app(DownloadAsset::class)->download(
            assetId: $assetId,
        );
    }
}
