<?php

namespace App\Actions\Assets;

use App\Models\Asset;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DownloadAsset
{
    public function download(int $assetId): StreamedResponse
    {
        $asset = Asset::query()->findOrFail($assetId);
        Gate::authorize('view', $asset);

        $disk = $asset->disk === 'local' ? 'public' : $asset->disk;
        $path = $asset->path;

        abort_unless(Storage::disk($disk)->exists($path), 404, 'File not found.');

        // Sanitize/encode the filename for headers
        $name = $asset->original_filename ?: basename($path);
        if (! mb_check_encoding($name, 'UTF-8')) {
            // fallback: coerce to UTF-8; if still messy, fall back to ASCII
            $name = @mb_convert_encoding($name, 'UTF-8', 'UTF-8, ISO-8859-1, Windows-1252') ?: Str::ascii($name);
        }

        return Storage::disk($disk)->response($path, $name, [
            'Content-Type' => $asset->mime_type,
            'Content-Disposition' => 'attachment; filename="'.$name.'"',
        ]);
    }
}
