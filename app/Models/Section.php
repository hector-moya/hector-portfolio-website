<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Section extends Model
{
    use HasFactory;

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
        return $this->belongsTo(Tab::class);
    }

    public function elements(): HasMany
    {
        return $this->hasMany(Field::class, 'section_id')->orderBy('sort_order');
    }
}
