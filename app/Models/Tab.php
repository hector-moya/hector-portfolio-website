<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $blueprint_id
 * @property string $name
 * @property string $handle
 * @property int $sort_order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Blueprint $blueprint
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Section> $sections
 * @property-read int|null $sections_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tab newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tab newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tab onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tab query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tab whereBlueprintId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tab whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tab whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tab whereHandle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tab whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tab whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tab whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tab whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tab withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tab withoutTrashed()
 * @mixin \Eloquent
 */
class Tab extends Model
{
    use HasFactory, SoftDeletes;

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
