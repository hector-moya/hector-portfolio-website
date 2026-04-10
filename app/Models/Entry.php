<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
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
 * @method static EntryFactory factory($count = null, $state = [])
 * @method static Builder<static>|Entry newModelQuery()
 * @method static Builder<static>|Entry newQuery()
 * @method static Builder<static>|Entry onlyTrashed()
 * @method static Builder<static>|Entry query()
 * @method static Builder<static>|Entry whereAuthorId($value)
 * @method static Builder<static>|Entry whereBlueprintId($value)
 * @method static Builder<static>|Entry whereCreatedAt($value)
 * @method static Builder<static>|Entry whereDeletedAt($value)
 * @method static Builder<static>|Entry whereId($value)
 * @method static Builder<static>|Entry whereLayout($value)
 * @method static Builder<static>|Entry wherePublishedAt($value)
 * @method static Builder<static>|Entry whereSlug($value)
 * @method static Builder<static>|Entry whereStatus($value)
 * @method static Builder<static>|Entry whereTitle($value)
 * @method static Builder<static>|Entry whereUpdatedAt($value)
 * @method static Builder<static>|Entry withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|Entry withoutTrashed()
 *
 * @mixin Model
 */
final class Entry extends Model
{
    use HasBlueprintFields;
    use HasFactory;
    use SoftDeletes;
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

    /**
     * Returns page builder sections, checking entry_elements first (new format),
     * then falling back to the legacy entries.layout column.
     *
     * @return array<int, array{_id: string, type: string, data: array<string, mixed>}>
     */
    public function getPageBuilderSections(): array
    {
        if ($this->relationLoaded('elements')) {
            $pbElement = $this->elements->first(
                fn (EntryElement $el): bool => $el->Field?->type === 'page_builder'
            );

            if ($pbElement !== null && is_array($pbElement->getElementValue())) {
                return $pbElement->getElementValue();
            }
        }

        return $this->layout ?? [];
    }

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'layout' => 'array',
        ];
    }
}
