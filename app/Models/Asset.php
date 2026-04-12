<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\AssetFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

/**
 * @property int $id
 * @property string $filename
 * @property string $original_filename
 * @property string $disk
 * @property string $mime_type
 * @property int $size
 * @property string $path
 * @property string|null $alt_text
 * @property string|null $caption
 * @property string|null $description
 * @property string|null $copyright
 * @property array{x: float, y: float}|null $focal_point
 * @property string|null $title
 * @property array<array-key, mixed>|null $meta
 * @property int $uploaded_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property int|null $folder_id
 * @property int|null $updated_by
 * @property-read Folder|null $folder
 * @property-read bool $is_image
 * @property-read string $size_for_humans
 * @property-read string $url
 * @property-read User|null $updater
 * @property-read User $uploader
 *
 * @method static AssetFactory factory($count = null, $state = [])
 * @method static Builder<static>|Asset newModelQuery()
 * @method static Builder<static>|Asset newQuery()
 * @method static Builder<static>|Asset onlyTrashed()
 * @method static Builder<static>|Asset query()
 * @method static Builder<static>|Asset whereAltText($value)
 * @method static Builder<static>|Asset whereCreatedAt($value)
 * @method static Builder<static>|Asset whereDeletedAt($value)
 * @method static Builder<static>|Asset whereDisk($value)
 * @method static Builder<static>|Asset whereFilename($value)
 * @method static Builder<static>|Asset whereFolderId($value)
 * @method static Builder<static>|Asset whereId($value)
 * @method static Builder<static>|Asset whereMeta($value)
 * @method static Builder<static>|Asset whereMimeType($value)
 * @method static Builder<static>|Asset whereOriginalFilename($value)
 * @method static Builder<static>|Asset wherePath($value)
 * @method static Builder<static>|Asset whereSize($value)
 * @method static Builder<static>|Asset whereTitle($value)
 * @method static Builder<static>|Asset whereUpdatedAt($value)
 * @method static Builder<static>|Asset whereUpdatedBy($value)
 * @method static Builder<static>|Asset whereUploadedBy($value)
 * @method static Builder<static>|Asset withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|Asset withoutTrashed()
 *
 * @mixin Model
 */
final class Asset extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'filename',
        'original_filename',
        'disk',
        'mime_type',
        'size',
        'path',
        'alt_text',
        'caption',
        'description',
        'copyright',
        'focal_point',
        'title',
        'folder_id',
        'meta',
        'uploaded_by',
        'updated_by',
    ];

    protected $casts = [
        'meta' => 'array',
        'focal_point' => 'array',
        'size' => 'integer',
    ];

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function delete()
    {
        $disk = Storage::disk($this->disk);

        $disk->delete($this->path);

        foreach (['thumbnail', 'medium'] as $variant) {
            if (! empty($this->meta[$variant])) {
                $disk->delete($this->meta[$variant]);
            }
        }

        return parent::delete();
    }

    public function folder(): BelongsTo
    {
        return $this->belongsTo(Folder::class, 'folder_id');
    }

    /**
     * Get the URL for the asset
     */
    protected function getUrlAttribute(): string
    {
        /** @var FilesystemAdapter */
        $disk = Storage::disk($this->disk);

        return $disk->url($this->path);
    }

    protected function getSizeForHumansAttribute(): string
    {
        $bytes = $this->size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2).' '.$units[$i];
    }

    protected function getIsImageAttribute(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    protected function getThumbnailUrlAttribute(): ?string
    {
        $path = $this->meta['thumbnail'] ?? null;

        return $path ? Storage::disk($this->disk)->url($path) : null;
    }

    protected function getMediumUrlAttribute(): ?string
    {
        $path = $this->meta['medium'] ?? null;

        return $path ? Storage::disk($this->disk)->url($path) : null;
    }
}
