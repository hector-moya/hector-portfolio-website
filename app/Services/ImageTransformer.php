<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class ImageTransformer
{
    public function __construct() {}

    /**
     * Generate thumbnail (150×150 cropped) and medium (800px wide) variants.
     *
     * @return array{thumbnail: string|null, medium: string|null}
     */
    public function transform(string $sourcePath, string $disk): array
    {
        $storage = Storage::disk($disk);

        if (! $storage->exists($sourcePath)) {
            return ['thumbnail' => null, 'medium' => null];
        }

        $mimeType = $storage->mimeType($sourcePath);
        if (! str_starts_with((string) $mimeType, 'image/')) {
            return ['thumbnail' => null, 'medium' => null];
        }

        $contents = $storage->get($sourcePath);
        if ($contents === null || $contents === '' || $contents === '0') {
            return ['thumbnail' => null, 'medium' => null];
        }

        $source = @imagecreatefromstring($contents);
        if ($source === false) {
            return ['thumbnail' => null, 'medium' => null];
        }

        $originalWidth = imagesx($source);
        $originalHeight = imagesy($source);
        $dir = dirname($sourcePath);
        $filename = pathinfo($sourcePath, PATHINFO_FILENAME);
        $ext = strtolower(pathinfo($sourcePath, PATHINFO_EXTENSION));

        $thumbnailPath = $dir.'/'.$filename.'_thumb.'.$ext;
        $mediumPath = $dir.'/'.$filename.'_medium.'.$ext;

        $this->saveThumbnail($source, $originalWidth, $originalHeight, $thumbnailPath, $disk, $ext);
        $this->saveMedium($source, $originalWidth, $originalHeight, $mediumPath, $disk, $ext);

        imagedestroy($source);

        return [
            'thumbnail' => $thumbnailPath,
            'medium' => $mediumPath,
        ];
    }

    private function saveThumbnail(\GdImage $source, int $srcW, int $srcH, string $destPath, string $disk, string $ext): void
    {
        $size = 150;

        $ratio = $srcW / $srcH;
        if ($ratio > 1) {
            $cropW = (int) ($srcH * 1);
            $cropH = $srcH;
            $cropX = (int) (($srcW - $cropW) / 2);
            $cropY = 0;
        } else {
            $cropW = $srcW;
            $cropH = (int) ($srcW / 1);
            $cropX = 0;
            $cropY = (int) (($srcH - $cropH) / 2);
        }

        $thumb = imagecreatetruecolor($size, $size);
        imagecopyresampled($thumb, $source, 0, 0, $cropX, $cropY, $size, $size, $cropW, $cropH);

        Storage::disk($disk)->put($destPath, $this->encodeImage($thumb, $ext));
        imagedestroy($thumb);
    }

    private function saveMedium(\GdImage $source, int $srcW, int $srcH, string $destPath, string $disk, string $ext): void
    {
        $maxWidth = 800;

        if ($srcW <= $maxWidth) {
            return;
        }

        $ratio = $maxWidth / $srcW;
        $newWidth = $maxWidth;
        $newHeight = (int) ($srcH * $ratio);

        $medium = imagecreatetruecolor($newWidth, $newHeight);
        imagecopyresampled($medium, $source, 0, 0, 0, 0, $newWidth, $newHeight, $srcW, $srcH);

        Storage::disk($disk)->put($destPath, $this->encodeImage($medium, $ext));
        imagedestroy($medium);
    }

    private function encodeImage(\GdImage $image, string $ext): string
    {
        ob_start();

        match ($ext) {
            'jpg', 'jpeg' => imagejpeg($image, null, 85),
            'png' => imagepng($image, null, 6),
            'gif' => imagegif($image),
            'webp' => imagewebp($image, null, 85),
            default => imagejpeg($image, null, 85),
        };

        return (string) ob_get_clean();
    }
}
