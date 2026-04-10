<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\FolderFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property int|null $parent_id
 * @property string $path
 * @property int $created_by
 * @property int|null $updated_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, Asset> $assets
 * @property-read int|null $assets_count
 * @property-read Collection<int, Folder> $children
 * @property-read int|null $children_count
 * @property-read User $creator
 * @property-read Folder|null $parent
 * @property-read User|null $updater
 *
 * @method static FolderFactory factory($count = null, $state = [])
 * @method static Builder<static>|Folder newModelQuery()
 * @method static Builder<static>|Folder newQuery()
 * @method static Builder<static>|Folder onlyTrashed()
 * @method static Builder<static>|Folder query()
 * @method static Builder<static>|Folder root()
 * @method static Builder<static>|Folder whereCreatedAt($value)
 * @method static Builder<static>|Folder whereCreatedBy($value)
 * @method static Builder<static>|Folder whereDeletedAt($value)
 * @method static Builder<static>|Folder whereId($value)
 * @method static Builder<static>|Folder whereName($value)
 * @method static Builder<static>|Folder whereParentId($value)
 * @method static Builder<static>|Folder wherePath($value)
 * @method static Builder<static>|Folder whereUpdatedAt($value)
 * @method static Builder<static>|Folder whereUpdatedBy($value)
 * @method static Builder<static>|Folder withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|Folder withoutTrashed()
 *
 * @mixin Model
 */
final class Folder extends Model
{
    use HasFactory;
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'parent_id',
        'path',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public static function makePath(?self $parent, string $name): string
    {
        $segment = mb_trim($name, '/');

        return $parent instanceof self ? mb_rtrim($parent->path, '/').'/'.$segment : $segment;
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class, 'folder_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function ancestors(): array
    {
        $trim = mb_trim($this->path, '/');
        if ($trim === '') {
            return [];
        }

        $bits = explode('/', $trim);
        $paths = [];
        $accum = '';
        foreach ($bits as $bit) {
            $accum = $accum === '' ? $bit : $accum.'/'.$bit;
            $paths[] = $accum;
        }

        return self::query()->whereIn('path', $paths)->orderByRaw('LENGTH(path)')->get()->all();
    }

    #[Scope]
    protected function root($query): mixed
    {
        return $query->whereNull('parent_id');
    }
}
