<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\CollectionFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int|null $parent_id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property int|null $blueprint_id
 * @property bool $is_active
 * @property array<array-key, mixed>|null $settings
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Blueprint|null $blueprint
 * @property-read Collection|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Collection> $children
 * @property-read int|null $children_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Entry> $entries
 * @property-read int|null $entries_count
 *
 * @method static CollectionFactory factory($count = null, $state = [])
 * @method static Builder<static>|Collection newModelQuery()
 * @method static Builder<static>|Collection newQuery()
 * @method static Builder<static>|Collection onlyTrashed()
 * @method static Builder<static>|Collection query()
 * @method static Builder<static>|Collection whereBlueprintId($value)
 * @method static Builder<static>|Collection whereCreatedAt($value)
 * @method static Builder<static>|Collection whereDeletedAt($value)
 * @method static Builder<static>|Collection whereDescription($value)
 * @method static Builder<static>|Collection whereId($value)
 * @method static Builder<static>|Collection whereIsActive($value)
 * @method static Builder<static>|Collection whereName($value)
 * @method static Builder<static>|Collection whereSettings($value)
 * @method static Builder<static>|Collection whereSlug($value)
 * @method static Builder<static>|Collection whereUpdatedAt($value)
 * @method static Builder<static>|Collection withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|Collection withoutTrashed()
 *
 * @mixin Model
 */
final class Collection extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'blueprint_id',
        'parent_id',
        'is_active',
        'settings',
    ];

    public function blueprint(): BelongsTo
    {
        return $this->belongsTo(Blueprint::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function entries(): HasMany
    {
        return $this->hasMany(Entry::class, 'blueprint_id', 'blueprint_id');
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'settings' => 'array',
        ];
    }
}
