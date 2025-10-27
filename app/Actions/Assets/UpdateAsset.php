<?php

namespace App\Actions\Assets;

use App\Models\Asset;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class UpdateAsset
{
    public function update(array $assetData): Asset
    {
        $asset = \App\Models\Asset::query()->findOrFail($assetData['id']);
        Gate::authorize('update', $asset);

        return DB::transaction(fn () => $asset->update([
            'filename' => $assetData['filename'],
            'original_filename' => $assetData['original_filename'],
            'disk' => $assetData['disk'],
            'mime_type' => $assetData['mime_type'],
            'size' => $assetData['size'],
            'path' => $assetData['path'],
            'alt_text' => $assetData['alt_text'],
            'title' => $assetData['title'],
            'folder' => $assetData['folder'],
            'meta' => $assetData['meta'],
            'uploaded_by' => $assetData['uploaded_by'],
        ]));

    }
}
