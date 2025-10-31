<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $id
 * @property string $handle
 * @property array<array-key, mixed>|null $value
 * @property string $global_set_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\GlobalSet $globalSet
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GlobalVariable newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GlobalVariable newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GlobalVariable query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GlobalVariable whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GlobalVariable whereGlobalSetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GlobalVariable whereHandle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GlobalVariable whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GlobalVariable whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GlobalVariable whereValue($value)
 *
 * @mixin \Illuminate\Database\Eloquent\Model
 */
class GlobalVariable extends Model
{
    use HasUlids;
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'handle',
        'value',
        'global_set_id',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'value' => 'array',
        ];
    }

    /**
     * Get the global set that owns the variable.
     */
    public function globalSet(): BelongsTo
    {
        return $this->belongsTo(GlobalSet::class);
    }
}
