<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $locale
 * @property string $translatable_type
 * @property int $translatable_id
 * @property string $field
 * @property string $value
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Model|Model $translatable
 *
 * @method static Builder<static>|Translation newModelQuery()
 * @method static Builder<static>|Translation newQuery()
 * @method static Builder<static>|Translation query()
 * @method static Builder<static>|Translation whereCreatedAt($value)
 * @method static Builder<static>|Translation whereField($value)
 * @method static Builder<static>|Translation whereId($value)
 * @method static Builder<static>|Translation whereLocale($value)
 * @method static Builder<static>|Translation whereTranslatableId($value)
 * @method static Builder<static>|Translation whereTranslatableType($value)
 * @method static Builder<static>|Translation whereUpdatedAt($value)
 * @method static Builder<static>|Translation whereValue($value)
 *
 * @mixin Model
 */
final class Translation extends Model
{
    use HasFactory;

    protected $fillable = [
        'locale',
        'translatable_type',
        'translatable_id',
        'field',
        'value',
    ];

    public function translatable(): MorphTo
    {
        return $this->morphTo();
    }
}
