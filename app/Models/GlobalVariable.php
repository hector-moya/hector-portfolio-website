<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read \App\Models\GlobalSet|null $globalSet
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GlobalVariable newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GlobalVariable newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GlobalVariable query()
 *
 * @mixin \Eloquent
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
