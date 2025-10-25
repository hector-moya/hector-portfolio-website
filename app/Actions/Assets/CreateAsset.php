<?php

namespace App\Actions\Assets;

use App\Models\Asset;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;

class CreateAsset
{
    public function create(array $assetData): Asset
    {
        Gate::authorize('create', Asset::class);

        return DB::transaction(function () use ($assetData) {
            return Asset::create([
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
            ]);
        });

    }
}
