<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\TaxonomyFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $handle
 * @property string $name
 * @property bool $hierarchical
 * @property bool $single_select
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, Term> $terms
 * @property-read int|null $terms_count
 *
 * @method static TaxonomyFactory factory($count = null, $state = [])
 * @method static Builder<static>|Taxonomy newModelQuery()
 * @method static Builder<static>|Taxonomy newQuery()
 * @method static Builder<static>|Taxonomy onlyTrashed()
 * @method static Builder<static>|Taxonomy query()
 * @method static Builder<static>|Taxonomy whereCreatedAt($value)
 * @method static Builder<static>|Taxonomy whereDeletedAt($value)
 * @method static Builder<static>|Taxonomy whereHandle($value)
 * @method static Builder<static>|Taxonomy whereHierarchical($value)
 * @method static Builder<static>|Taxonomy whereId($value)
 * @method static Builder<static>|Taxonomy whereName($value)
 * @method static Builder<static>|Taxonomy whereSingleSelect($value)
 * @method static Builder<static>|Taxonomy whereUpdatedAt($value)
 * @method static Builder<static>|Taxonomy withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|Taxonomy withoutTrashed()
 *
 * @mixin Model
 */
final class Taxonomy extends Model
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
