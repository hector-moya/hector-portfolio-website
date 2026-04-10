<?php

declare(strict_types=1);

namespace App\Actions\Assets;

use App\Models\Asset;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

final class DeleteAsset
{
    public function delete(array $assetData): void
    {
        $asset = Asset::query()->findOrFail($assetData['id']);
        Gate::authorize('delete', $asset);

        Storage::disk($asset->disk)->delete($asset->path);

        DB::transaction(function () use ($asset): void {
            $asset->delete();
        });
    }
}
