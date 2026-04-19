<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Database\Factories\BlueprintFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property bool $is_active
 * @property array|null $settings
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Collection> $collections
 * @property-read int|null $collections_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Entry> $entries
 * @property-read int|null $entries_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Field> $fields
 * @property-read int|null $fields_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Section> $sections
 * @property-read int|null $sections_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Tab> $tabs
 * @property-read int|null $tabs_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Translation> $translations
 * @property-read int|null $translations_count
 *
 * @method static BlueprintFactory factory($count = null, $state = [])
 * @method static Builder<static>|Blueprint newModelQuery()
 * @method static Builder<static>|Blueprint newQuery()
 * @method static Builder<static>|Blueprint onlyTrashed()
 * @method static Builder<static>|Blueprint query()
 * @method static Builder<static>|Blueprint whereCreatedAt($value)
 * @method static Builder<static>|Blueprint whereDeletedAt($value)
 * @method static Builder<static>|Blueprint whereDescription($value)
 * @method static Builder<static>|Blueprint whereId($value)
 * @method static Builder<static>|Blueprint whereIsActive($value)
 * @method static Builder<static>|Blueprint whereName($value)
 * @method static Builder<static>|Blueprint whereSlug($value)
 * @method static Builder<static>|Blueprint whereUpdatedAt($value)
 * @method static Builder<static>|Blueprint withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|Blueprint withoutTrashed()
 *
 * @mixin Model
 */
final class Blueprint extends Model
{
    use HasFactory;
    use HasTranslations;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
        'settings',
        'order',
    ];

    /**
     * The attributes that are translatable.
     *
     * @var array<string>
     */
    private array $translatable = [
        'name',
        'description',
    ];

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

    protected static function booted(): void
    {
        self::creating(function (self $model): void {
            if ($model->order === 0 || $model->order === null) {
                $model->order = (self::query()->max('order') ?? -1) + 1;
            }
        });

        self::deleting(function (self $model): void {
            $model->updateQuietly(['slug' => sprintf('%s__deleted_%d', $model->slug, $model->id)]);
        });
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'settings' => 'array',
        ];
    }
}
