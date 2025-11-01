<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BlueprintSection extends Model
{
    protected $fillable = [
        'name',
        'handle',
        'instructions',
        'sort_order',
    ];

    public function blueprint(): BelongsTo
    {
        return $this->belongsTo(Blueprint::class);
    }

    public function tab(): BelongsTo
    {
        return $this->belongsTo(BlueprintTab::class);
    }

    public function elements(): HasMany
    {
        return $this->hasMany(BlueprintElement::class, 'section_id')->orderBy('sort_order');
    }
}
