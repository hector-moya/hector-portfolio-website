<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $entry_id
 * @property int $blueprint_element_id
 * @property string $handle
 * @property string|null $value
 * @property array<array-key, mixed>|null $meta
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\BlueprintElement $blueprintElement
 * @property-read \App\Models\Entry $entry
 *
 * @method static \Database\Factories\EntryElementFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EntryElement newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EntryElement newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EntryElement query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EntryElement whereBlueprintElementId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EntryElement whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EntryElement whereEntryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EntryElement whereHandle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EntryElement whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EntryElement whereMeta($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EntryElement whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EntryElement whereValue($value)
 *
 * @mixin \Illuminate\Database\Eloquent\Model
 */
class EntryElement extends Model
{
    /** @use HasFactory<\Database\Factories\EntryElementFactory> */
    use HasFactory;

    protected $fillable = [
        'entry_id',
        'blueprint_element_id',
        'handle',
        'value',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'meta' => 'array',
        ];
    }

    public function entry(): BelongsTo
    {
        return $this->belongsTo(Entry::class);
    }

    public function blueprintElement(): BelongsTo
    {
        return $this->belongsTo(BlueprintElement::class);
    }
}
