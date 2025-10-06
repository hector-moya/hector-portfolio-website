<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Entry extends Model
{
    /** @use HasFactory<\Database\Factories\EntryFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'collection_id',
        'blueprint_id',
        'title',
        'slug',
        'status',
        'author_id',
        'published_at',
        'layout',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'layout' => 'array',
        ];
    }

    public function collection(): BelongsTo
    {
        return $this->belongsTo(Collection::class);
    }

    public function blueprint(): BelongsTo
    {
        return $this->belongsTo(Blueprint::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function elements(): HasMany
    {
        return $this->hasMany(EntryElement::class);
    }
}
