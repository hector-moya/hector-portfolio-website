<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $name
 * @property int|null $parent_id
 * @property string $path
 * @property int $created_by
 * @property int|null $updated_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Asset> $assets
 * @property-read int|null $assets_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Folder> $children
 * @property-read int|null $children_count
 * @property-read \App\Models\User $creator
 * @property-read Folder|null $parent
 * @property-read \App\Models\User|null $updater
 *
 * @method static \Database\Factories\FolderFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Folder newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Folder newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Folder onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Folder query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Folder root()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Folder whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Folder whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Folder whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Folder whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Folder whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Folder whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Folder wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Folder whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Folder whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Folder withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Folder withoutTrashed()
 *
 * @mixin \Illuminate\Database\Eloquent\Model
 */
class Folder extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;
    use \Illuminate\Database\Eloquent\Factories\HasFactory;
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

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Folder::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Folder::class, 'parent_id');
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

    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    protected function root($query): mixed
    {
        return $query->whereNull('parent_id');
    }

    public static function makePath(?Folder $parent, string $name): string
    {
        $segment = trim($name, '/');

        return $parent instanceof \App\Models\Folder ? rtrim($parent->path, '/').'/'.$segment : $segment;
    }

    public function ancestors(): array
    {
        $trim = trim($this->path, '/');
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

        return static::query()->whereIn('path', $paths)->orderByRaw('LENGTH(path)')->get()->all();
    }
}
