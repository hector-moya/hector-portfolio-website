<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\NavigationFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string $handle
 * @property string|null $description
 * @property bool $is_active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, NavigationItem> $items
 * @property-read int|null $items_count
 *
 * @method static NavigationFactory factory($count = null, $state = [])
 * @method static Builder<static>|Navigation newModelQuery()
 * @method static Builder<static>|Navigation newQuery()
 * @method static Builder<static>|Navigation query()
 * @method static Builder<static>|Navigation whereCreatedAt($value)
 * @method static Builder<static>|Navigation whereDescription($value)
 * @method static Builder<static>|Navigation whereHandle($value)
 * @method static Builder<static>|Navigation whereId($value)
 * @method static Builder<static>|Navigation whereIsActive($value)
 * @method static Builder<static>|Navigation whereName($value)
 * @method static Builder<static>|Navigation whereUpdatedAt($value)
 *
 * @mixin Model
 */
final class Navigation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'handle',
        'description',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the items in this navigation menu.
     */
    public function items(): HasMany
    {
        return $this->hasMany(NavigationItem::class)->orderBy('order');
    }
}
