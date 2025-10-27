<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

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
