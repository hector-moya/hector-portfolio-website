<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BlueprintElement extends Model
{
    /** @use HasFactory<\Database\Factories\BlueprintElementFactory> */
    use HasFactory;

    protected $fillable = [
        'blueprint_id',
        'type',
        'label',
        'handle',
        'instructions',
        'config',
        'is_required',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'config' => 'array',
            'is_required' => 'boolean',
            'order' => 'integer',
        ];
    }

    public function blueprint(): BelongsTo
    {
        return $this->belongsTo(Blueprint::class);
    }

    public function entryElements(): HasMany
    {
        return $this->hasMany(EntryElement::class);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}
