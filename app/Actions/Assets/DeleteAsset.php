<?php

namespace App\Actions\Assets;

use App\Models\Asset;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;

class DeleteAsset
{
    public function delete(array $assetData): void
    {
        $asset = Asset::findOrFail($assetData['id']);
        Gate::authorize('delete', $asset);

        Storage::disk($asset->disk)->delete($asset->path);

        DB::transaction(function () use ($asset) {
            $asset->delete();
        });
    }
}
