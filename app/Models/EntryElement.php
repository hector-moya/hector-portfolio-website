<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\EntryElementFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $entry_id
 * @property int $field_id
 * @property string $handle
 * @property string|null $value
 * @property array<array-key, mixed>|null $meta
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read Field $field
 * @property-read Entry $entry
 *
 * @method static EntryElementFactory factory($count = null, $state = [])
 * @method static Builder<static>|EntryElement newModelQuery()
 * @method static Builder<static>|EntryElement newQuery()
 * @method static Builder<static>|EntryElement query()
 * @method static Builder<static>|EntryElement whereCreatedAt($value)
 * @method static Builder<static>|EntryElement whereDeletedAt($value)
 * @method static Builder<static>|EntryElement whereEntryId($value)
 * @method static Builder<static>|EntryElement whereFieldId($value)
 * @method static Builder<static>|EntryElement whereHandle($value)
 * @method static Builder<static>|EntryElement whereId($value)
 * @method static Builder<static>|EntryElement whereMeta($value)
 * @method static Builder<static>|EntryElement whereUpdatedAt($value)
 * @method static Builder<static>|EntryElement whereValue($value)
 *
 * @mixin Model
 */
final class EntryElement extends Model
{
    /** @use HasFactory<EntryElementFactory> */
    use HasFactory;

    protected $fillable = [
        'entry_id',
        'field_id',
        'handle',
        'value',
        'meta',
    ];

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

    public function field(): BelongsTo
    {
        return $this->belongsTo(Field::class);
    }

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
}
