<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasBlueprintFields;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property-read \App\Models\Blueprint|null $blueprint
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\GlobalVariable> $variables
 * @property-read int|null $variables_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GlobalSet newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GlobalSet newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GlobalSet query()
 * @mixin \Eloquent
 */
class GlobalSet extends Model
{
    use HasBlueprintFields, HasUlids;
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'handle',
        'name',
        'blueprint_id',
    ];

    /**
     * Get the blueprint that owns the global set.
     */
    public function blueprint(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Blueprint::class);
    }

    /**
     * Get the variables for this global set.
     */
    public function variables(): HasMany
    {
        return $this->hasMany(GlobalVariable::class);
    }
}
