<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int $id
 * @property string $locale
 * @property string $translatable_type
 * @property int $translatable_id
 * @property string $field
 * @property string $value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Model|\Eloquent $translatable
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Translation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Translation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Translation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Translation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Translation whereField($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Translation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Translation whereLocale($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Translation whereTranslatableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Translation whereTranslatableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Translation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Translation whereValue($value)
 *
 * @mixin \Eloquent
 */
class Translation extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

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
