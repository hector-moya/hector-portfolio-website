<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
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
 * @property string|null $title
 * @property array<array-key, mixed>|null $meta
 * @property int $uploaded_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $folder_id
 * @property int|null $updated_by
 * @property-read \App\Models\Folder|null $folder
 * @property-read bool $is_image
 * @property-read string $size_for_humans
 * @property-read string $url
 * @property-read \App\Models\User|null $updater
 * @property-read \App\Models\User $uploader
 * @method static \Database\Factories\AssetFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereAltText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereDisk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereFilename($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereFolderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereMeta($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereMimeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereOriginalFilename($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereUploadedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset withoutTrashed()
 * @mixin \Eloquent
 */
class Asset extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'filename',
        'original_filename',
        'disk',
        'mime_type',
        'size',
        'path',
        'alt_text',
        'title',
        'folder_id',
        'meta',
        'uploaded_by',
        'updated_by',
    ];

    protected $casts = [
        'meta' => 'array',
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

    /**
     * Get the URL for the asset
     */
    protected function getUrlAttribute(): string
    {
        /** @var \Illuminate\Filesystem\FilesystemAdapter */
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

    public function delete()
    {
        // Delete the physical file
        Storage::disk($this->disk)->delete($this->path);

        return parent::delete();
    }

    public function folder(): BelongsTo
    {
        return $this->belongsTo(Folder::class, 'folder_id');
    }
}
