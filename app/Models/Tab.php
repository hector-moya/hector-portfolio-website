<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tab extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'handle',
        'blueprint_id',
        'sort_order',
    ];

    public function blueprint(): BelongsTo
    {
        return $this->belongsTo(Blueprint::class);
    }

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class, 'tab_id')->orderBy('sort_order');
    }
}
