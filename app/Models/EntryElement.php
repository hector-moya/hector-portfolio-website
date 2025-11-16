<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $entry_id
 * @property int $field_id
 * @property string $handle
 * @property string|null $value
 * @property array<array-key, mixed>|null $meta
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \App\Models\Field $Field
 * @property-read \App\Models\Entry $entry
 * @method static \Database\Factories\EntryElementFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EntryElement newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EntryElement newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EntryElement query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EntryElement whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EntryElement whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EntryElement whereEntryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EntryElement whereFieldId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EntryElement whereHandle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EntryElement whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EntryElement whereMeta($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EntryElement whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EntryElement whereValue($value)
 * @mixin \Eloquent
 */
class EntryElement extends Model
{
    /** @use HasFactory<\Database\Factories\EntryElementFactory> */
    use HasFactory;

    protected $fillable = [
        'entry_id',
        'field_id',
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

    protected function shouldUseMetaForValue(mixed $value): bool
    {
        return is_array($value);
    }

    /**
     * Get the value for this element, checking meta for arrays
     */
    public function getElementValue(): mixed
    {
        return $this->meta ?? $this->value;
    }

    /**
     * Get the raw value without transformation
     */
    public function getRawValue(): mixed
    {
        return $this->attributes['value'] ?? null;
    }

    /**
     * Set the value for this element, using meta for arrays
     */
    public function setElementValue(mixed $value): void
    {
        if ($this->shouldUseMetaForValue($value)) {
            $this->meta = $value;
            $this->attributes['value'] = null;
        } else {
            $this->attributes['value'] = $value;
            $this->meta = null;
        }
    }

    public function getValue(): mixed
    {
        return $this->meta ?? $this->attributes['value'];
    }

    public function entry(): BelongsTo
    {
        return $this->belongsTo(Entry::class);
    }

    public function Field(): BelongsTo
    {
        return $this->belongsTo(Field::class);
    }
}
