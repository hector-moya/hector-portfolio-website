<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GlobalSet extends Model
{
    use HasUlids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'handle',
        'name',
        'blueprint_id',
    ];

    /**
     * Get the blueprint that owns the global set.
     */
    public function blueprint()
    {
        return $this->belongsTo(Blueprint::class);
    }

    /**
     * Get the variables for this global set.
     */
    public function variables(): HasMany
    {
        return $this->hasMany(GlobalVariable::class);
    }
}
