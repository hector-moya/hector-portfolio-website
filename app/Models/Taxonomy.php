<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $handle
 * @property string $name
 * @property bool $hierarchical
 * @property bool $single_select
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Term> $terms
 * @property-read int|null $terms_count
 *
 * @method static \Database\Factories\TaxonomyFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Taxonomy newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Taxonomy newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Taxonomy onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Taxonomy query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Taxonomy whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Taxonomy whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Taxonomy whereHandle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Taxonomy whereHierarchical($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Taxonomy whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Taxonomy whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Taxonomy whereSingleSelect($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Taxonomy whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Taxonomy withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Taxonomy withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Taxonomy extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'handle',
        'name',
        'hierarchical',
        'single_select',
    ];

    protected $casts = [
        'hierarchical' => 'boolean',
        'single_select' => 'boolean',
    ];

    public function terms(): HasMany
    {
        return $this->hasMany(Term::class);
    }
}
