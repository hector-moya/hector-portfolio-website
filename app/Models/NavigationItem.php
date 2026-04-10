<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Database\Factories\NavigationItemFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $navigation_id
 * @property int|null $parent_id
 * @property string $title
 * @property string|null $url
 * @property int $order
 * @property string $target
 * @property string|null $linkable_type
 * @property int|null $linkable_id
 * @property string|null $class
 * @property bool $is_active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, NavigationItem> $children
 * @property-read int|null $children_count
 * @property-read Model|Model|null $linkable
 * @property-read Navigation $navigation
 * @property-read NavigationItem|null $parent
 *
 * @method static NavigationItemFactory factory($count = null, $state = [])
 * @method static Builder<static>|NavigationItem newModelQuery()
 * @method static Builder<static>|NavigationItem newQuery()
 * @method static Builder<static>|NavigationItem query()
 * @method static Builder<static>|NavigationItem whereClass($value)
 * @method static Builder<static>|NavigationItem whereCreatedAt($value)
 * @method static Builder<static>|NavigationItem whereId($value)
 * @method static Builder<static>|NavigationItem whereIsActive($value)
 * @method static Builder<static>|NavigationItem whereLinkableId($value)
 * @method static Builder<static>|NavigationItem whereLinkableType($value)
 * @method static Builder<static>|NavigationItem whereNavigationId($value)
 * @method static Builder<static>|NavigationItem whereOrder($value)
 * @method static Builder<static>|NavigationItem whereParentId($value)
 * @method static Builder<static>|NavigationItem whereTarget($value)
 * @method static Builder<static>|NavigationItem whereTitle($value)
 * @method static Builder<static>|NavigationItem whereUpdatedAt($value)
 * @method static Builder<static>|NavigationItem whereUrl($value)
 *
 * @mixin Model
 */
final class NavigationItem extends Model
{
    /** @use HasFactory<NavigationItemFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'navigation_id',
        'parent_id',
        'title',
        'url',
        'order',
        'target',
        'linkable_type',
        'linkable_id',
        'class',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'order' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get the navigation menu this item belongs to.
     */
    public function navigation(): BelongsTo
    {
        return $this->belongsTo(Navigation::class);
    }

    /**
     * Get the parent navigation item.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * Get the child navigation items.
     */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('order');
    }

    /**
     * Get the linked model (Entry, Asset, etc).
     */
    public function linkable(): MorphTo
    {
        return $this->morphTo();
    }
}
