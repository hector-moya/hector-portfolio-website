<?php

namespace App\Actions\Assets;

use App\Models\Asset;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class MoveAsset
{
    public function move(int $assetId, string $targetFolder, ?int $folderId = null): void
    {
        $asset = Asset::query()->findOrFail($assetId);
        Gate::authorize('update', $asset);

        $disk = Storage::disk($asset->disk);
        $prefix = $targetFolder !== '' ? trim($targetFolder, '/').'/' : '';

        $newPath = $prefix.$asset->filename;
        $disk->move($asset->path, $newPath);

        $meta = $asset->meta ?? [];

        foreach (['thumbnail', 'medium'] as $variant) {
            if (! empty($meta[$variant])) {
                $newVariantPath = $prefix.basename($meta[$variant]);
                $disk->move($meta[$variant], $newVariantPath);
                $meta[$variant] = $newVariantPath;
            }
        }

        $asset->update([
            'path' => $newPath,
            'folder_id' => $folderId,
            'meta' => $meta,
        ]);
    }
}
