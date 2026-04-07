<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $taxonomy_id
 * @property string $slug
 * @property string $name
 * @property int|null $parent_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, Term> $children
 * @property-read int|null $children_count
 * @property-read Collection<int, Entry> $entries
 * @property-read int|null $entries_count
 * @property-read Term|null $parent
 * @property-read Taxonomy $taxonomy
 *
 * @method static \Database\Factories\TermFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Term newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Term newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Term onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Term query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Term whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Term whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Term whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Term whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Term whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Term whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Term whereTaxonomyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Term whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Term withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Term withoutTrashed()
 *
 * @mixin Model
 */
class Term extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'taxonomy_id',
        'slug',
        'name',
        'parent_id',
    ];

    public function taxonomy(): BelongsTo
    {
        return $this->belongsTo(Taxonomy::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Term::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Term::class, 'parent_id');
    }

    public function entries(): MorphToMany
    {
        return $this->morphedByMany(Entry::class, 'termable');
    }
}
