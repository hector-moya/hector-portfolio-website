<?php

namespace App\Models;

use App\Models\Concerns\HasBlueprintFields;
use Database\Factories\EntryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $blueprint_id
 * @property string $title
 * @property string $slug
 * @property string $status
 * @property int $author_id
 * @property Carbon|null $published_at
 * @property array<array-key, mixed>|null $layout
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read User $author
 * @property-read Blueprint $blueprint
 * @property-read Collection|null $collection
 * @property-read \Illuminate\Database\Eloquent\Collection<int, EntryElement> $elements
 * @property-read int|null $elements_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Term> $terms
 * @property-read int|null $terms_count
 *
 * @method static \Database\Factories\EntryFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Entry newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Entry newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Entry onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Entry query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Entry whereAuthorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Entry whereBlueprintId($value)
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
 * @mixin Model
 */
class Entry extends Model
{
    /** @use HasFactory<EntryFactory> */
    use HasBlueprintFields, HasFactory, SoftDeletes;

    protected $fillable = [
        'blueprint_id',
        'title',
        'slug',
        'excerpt',
        'status',
        'author_id',
        'published_at',
        'layout',
        'seo_title',
        'seo_description',
        'og_image',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'layout' => 'array',
        ];
    }

    public function blueprint(): BelongsTo
    {
        return $this->belongsTo(Blueprint::class);
    }

    public function collection(): HasOne
    {
        return $this->hasOne(Collection::class, 'blueprint_id', 'blueprint_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function elements(): HasMany
    {
        return $this->hasMany(EntryElement::class);
    }

    public function terms(): MorphToMany
    {
        return $this->morphToMany(Term::class, 'termable');
    }

    public function termsIn(string $taxonomyHandle)
    {
        return $this->terms()->whereHas('taxonomy', fn ($q) => $q->where('handle', $taxonomyHandle));
    }
}
