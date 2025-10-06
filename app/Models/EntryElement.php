<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EntryElement extends Model
{
    /** @use HasFactory<\Database\Factories\EntryElementFactory> */
    use HasFactory;

    protected $fillable = [
        'entry_id',
        'blueprint_element_id',
        'handle',
        'value',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'meta' => 'array',
        ];
    }

    public function entry(): BelongsTo
    {
        return $this->belongsTo(Entry::class);
    }

    public function blueprintElement(): BelongsTo
    {
        return $this->belongsTo(BlueprintElement::class);
    }
}
