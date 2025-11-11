<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

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
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $section_id
 * @property int|null $parent_id
 * @property-read \App\Models\Blueprint $blueprint
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Field> $children
 * @property-read int|null $children_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\EntryElement> $entryElements
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
 * @mixin \Eloquent
 */
class Field extends Model
{
    /** @use HasFactory<\Database\Factories\FieldFactory> */
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

    protected function casts(): array
    {
        return [
            'config' => 'array',
            'is_required' => 'boolean',
            'order' => 'integer',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Field::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Field::class, 'parent_id');
    }

    public function blueprint(): BelongsTo
    {
        return $this->belongsTo(Blueprint::class);
    }

    public function entryElements(): HasMany
    {
        return $this->hasMany(EntryElement::class);
    }

    #[Scope]
    protected function ordered($query)
    {
        return $query->orderBy('order');
    }
}
