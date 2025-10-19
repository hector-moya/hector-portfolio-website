<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
 * @property-read \App\Models\Blueprint $blueprint
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\EntryElement> $entryElements
 * @property-read int|null $entry_elements_count
 *
 * @method static \Database\Factories\BlueprintElementFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BlueprintElement newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BlueprintElement newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BlueprintElement ordered()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BlueprintElement query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BlueprintElement whereBlueprintId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BlueprintElement whereConfig($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BlueprintElement whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BlueprintElement whereHandle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BlueprintElement whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BlueprintElement whereInstructions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BlueprintElement whereIsRequired($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BlueprintElement whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BlueprintElement whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BlueprintElement whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BlueprintElement whereUpdatedAt($value)
 *
 * @mixin \Illuminate\Database\Eloquent\Model
 */
class BlueprintElement extends Model
{
    /** @use HasFactory<\Database\Factories\BlueprintElementFactory> */
    use HasFactory;

    protected $fillable = [
        'blueprint_id',
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

    public function blueprint(): BelongsTo
    {
        return $this->belongsTo(Blueprint::class);
    }

    public function entryElements(): HasMany
    {
        return $this->hasMany(EntryElement::class);
    }

    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    protected function ordered($query)
    {
        return $query->orderBy('order');
    }
}
