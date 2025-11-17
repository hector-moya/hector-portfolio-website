<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property string $handle
 * @property string|null $description
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\NavigationItem> $items
 * @property-read int|null $items_count
 *
 * @method static \Database\Factories\NavigationFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Navigation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Navigation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Navigation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Navigation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Navigation whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Navigation whereHandle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Navigation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Navigation whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Navigation whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Navigation whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Navigation extends Model
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
