<?php

namespace App\Actions\Assets;

use App\Models\Asset;
use Illuminate\Support\Facades\DB;

class UpdateAsset
{
    public function update(array $assetData): Asset
    {
        $asset = Asset::query()->findOrFail($assetData['id']);
        // Gate::authorize('update', $asset);

        return DB::transaction(function () use ($asset, $assetData) {

            $asset->update([
                'filename' => $assetData['filename'],
                'original_filename' => $assetData['original_filename'],
                'disk' => $assetData['disk'],
                'mime_type' => $assetData['mime_type'],
                'size' => $assetData['size'],
                'path' => $assetData['path'],
                'alt_text' => $assetData['alt_text'],
                'caption' => $assetData['caption'] ?? null,
                'description' => $assetData['description'] ?? null,
                'copyright' => $assetData['copyright'] ?? null,
                'focal_point' => $assetData['focal_point'] ?? null,
                'title' => $assetData['title'],
                'folder_id' => $assetData['folder_id'],
                'meta' => $assetData['meta'],
                'uploaded_by' => $assetData['uploaded_by'],
            ]);

            return $asset;
        });

    }
}
