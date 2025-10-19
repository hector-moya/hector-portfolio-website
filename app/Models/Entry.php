<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $collection_id
 * @property int $blueprint_id
 * @property string $title
 * @property string $slug
 * @property string $status
 * @property int $author_id
 * @property \Illuminate\Support\Carbon|null $published_at
 * @property array<array-key, mixed>|null $layout
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User $author
 * @property-read \App\Models\Blueprint $blueprint
 * @property-read \App\Models\Collection $collection
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\EntryElement> $elements
 * @property-read int|null $elements_count
 *
 * @method static \Database\Factories\EntryFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Entry newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Entry newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Entry onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Entry query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Entry whereAuthorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Entry whereBlueprintId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Entry whereCollectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Entry whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Entry whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Entry whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Entry whereLayout($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Entry wherePublishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Entry whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Entry whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Entry whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Entry whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Entry withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Entry withoutTrashed()
 *
 * @mixin \Illuminate\Database\Eloquent\Model
 */
class Entry extends Model
{
    /** @use HasFactory<\Database\Factories\EntryFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'collection_id',
        'blueprint_id',
        'title',
        'slug',
        'status',
        'author_id',
        'published_at',
        'layout',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'layout' => 'array',
        ];
    }

    public function collection(): BelongsTo
    {
        return $this->belongsTo(Collection::class);
    }

    public function blueprint(): BelongsTo
    {
        return $this->belongsTo(Blueprint::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function elements(): HasMany
    {
        return $this->hasMany(EntryElement::class);
    }
}
