<?php

declare(strict_types=1);

namespace App\Actions\Assets;

use App\Models\Asset;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

final class CreateAsset
{
    public function create(array $assetData): Asset
    {
        Gate::authorize('create', Asset::class);

        return DB::transaction(fn () => Asset::query()->create([
            'filename' => $assetData['filename'],
            'original_filename' => $assetData['original_filename'],
            'disk' => $assetData['disk'],
            'mime_type' => $assetData['mime_type'],
            'size' => $assetData['size'],
            'path' => $assetData['path'],
            'alt_text' => $assetData['alt_text'],
            'title' => $assetData['title'],
            'folder_id' => $assetData['folder_id'],
            'meta' => $assetData['meta'],
            'uploaded_by' => $assetData['uploaded_by'],
            'updated_by' => $assetData['updated_by'],
        ]));

    }
}
