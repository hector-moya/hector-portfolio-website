<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use App\Models\Concerns\HasBlueprintFields;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property string $id
 * @property string $handle
 * @property string $name
 * @property int|null $blueprint_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Blueprint|null $blueprint
 * @property-read Collection<int, GlobalVariable> $variables
 * @property-read int|null $variables_count
 *
 * @method static Builder<static>|GlobalSet newModelQuery()
 * @method static Builder<static>|GlobalSet newQuery()
 * @method static Builder<static>|GlobalSet query()
 *
 * @mixin Model
 */
final class GlobalSet extends Model
{
    use HasBlueprintFields;
    use HasUlids;
    use HasFactory;

    protected $table = 'globals';

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
    public function blueprint(): BelongsTo
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
