<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\FieldFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $blueprint_id
 * @property string $type
 * @property string $label
 * @property string $handle
 * @property string|null $instructions
 * @property array<array-key, mixed>|null $config
 * @property bool $is_required
 * @property int $order
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property int|null $section_id
 * @property int|null $parent_id
 * @property-read Blueprint $blueprint
 * @property-read Collection<int, Field> $children
 * @property-read int|null $children_count
 * @property-read Collection<int, EntryElement> $entryElements
 * @property-read int|null $entry_elements_count
 * @property-read Field|null $parent
 *
 * @method static \Database\Factories\FieldFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Field newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Field newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Field onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Field ordered()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Field query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Field whereBlueprintId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Field whereConfig($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Field whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Field whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Field whereHandle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Field whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Field whereInstructions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Field whereIsRequired($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Field whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Field whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Field whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Field whereSectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Field whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Field whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Field withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Field withoutTrashed()
 *
 * @mixin Model
 */
final class Field extends Model
{
    /** @use HasFactory<FieldFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'blueprint_id',
        'section_id',
        'parent_id',
        'type',
        'label',
        'handle',
        'instructions',
        'config',
        'is_required',
        'order',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function blueprint(): BelongsTo
    {
        return $this->belongsTo(Blueprint::class);
    }

    public function entryElements(): HasMany
    {
        return $this->hasMany(EntryElement::class);
    }

    protected function casts(): array
    {
        return [
            'config' => 'array',
            'is_required' => 'boolean',
            'order' => 'integer',
        ];
    }

    #[Scope]
    protected function ordered($query)
    {
        return $query->orderBy('order');
    }
}
