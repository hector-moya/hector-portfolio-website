<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Collection> $collections
 * @property-read int|null $collections_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Field> $elements
 * @property-read int|null $elements_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Entry> $entries
 * @property-read int|null $entries_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Translation> $translations
 * @property-read int|null $translations_count
 *
 * @method static \Database\Factories\BlueprintFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Blueprint newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Blueprint newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Blueprint onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Blueprint query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Blueprint whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Blueprint whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Blueprint whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Blueprint whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Blueprint whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Blueprint whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Blueprint whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Blueprint whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Blueprint withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Blueprint withoutTrashed()
 *
 * @mixin \Illuminate\Database\Eloquent\Model
 */
class Blueprint extends Model
{
    /** @use HasFactory<\Database\Factories\BlueprintFactory> */
    use HasFactory, HasTranslations, SoftDeletes;

    /**
     * The attributes that are translatable.
     *
     * @var array<string>
     */
    protected $translatable = [
        'name',
        'description',
    ];

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function fields(): HasMany
    {
        return $this->hasMany(Field::class)->orderBy('order');
    }

    public function collections(): HasMany
    {
        return $this->hasMany(Collection::class);
    }

    public function entries(): HasMany
    {
        return $this->hasMany(Entry::class);
    }

    public function tabs(): HasMany
    {
        return $this->hasMany(Tab::class)->orderBy('sort_order');
    }

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class)->orderBy('sort_order');
    }
}
