<?php

namespace App\Actions\Assets;

use App\Models\Asset;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;

class DownloadAsset
{
    public function download(int $assetId): Response
    {
        $asset = Asset::query()->findOrFail($assetId);
        Gate::authorize('view', $asset);

        $path = $asset->path;
        $disk = $asset->disk;

        if (! Storage::disk($disk)->exists($path)) {
            return response('File not found.', 404);
        }

        $fileContent = Storage::disk($disk)->get($path);

        return response($fileContent)
            ->header('Content-Type', $asset->mime_type)
            ->header('Content-Disposition', 'attachment; filename="'.$asset->original_filename.'"');
    }
}
