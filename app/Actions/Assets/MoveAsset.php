<?php

namespace App\Actions\Assets;

use App\Models\Asset;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;

class MoveAsset
{
    public function move(int $assetId, string $targetFolder): void
    {
        $asset = Asset::query()->findOrFail($assetId);
        Gate::authorize('update', $asset);

        $oldPath = $asset->path;
        $newPath = trim((string) $targetFolder, '/').'/'.$asset->filename;

        Storage::disk($asset->disk)->move($oldPath, $newPath);

        $asset->update([
            'path' => $newPath,
            'folder' => $targetFolder,
        ]);
    }
}
