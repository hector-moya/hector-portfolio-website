<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $blueprint_id
 * @property string $name
 * @property string $handle
 * @property int $sort_order
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Blueprint $blueprint
 * @property-read Collection<int, Section> $sections
 * @property-read int|null $sections_count
 *
 * @method static Builder<static>|Tab newModelQuery()
 * @method static Builder<static>|Tab newQuery()
 * @method static Builder<static>|Tab onlyTrashed()
 * @method static Builder<static>|Tab query()
 * @method static Builder<static>|Tab whereBlueprintId($value)
 * @method static Builder<static>|Tab whereCreatedAt($value)
 * @method static Builder<static>|Tab whereDeletedAt($value)
 * @method static Builder<static>|Tab whereHandle($value)
 * @method static Builder<static>|Tab whereId($value)
 * @method static Builder<static>|Tab whereName($value)
 * @method static Builder<static>|Tab whereSortOrder($value)
 * @method static Builder<static>|Tab whereUpdatedAt($value)
 * @method static Builder<static>|Tab withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|Tab withoutTrashed()
 *
 * @mixin Model
 */
final class Tab extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'handle',
        'blueprint_id',
        'sort_order',
    ];

    public function blueprint(): BelongsTo
    {
        return $this->belongsTo(Blueprint::class);
    }

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class, 'tab_id')->orderBy('sort_order');
    }
}
