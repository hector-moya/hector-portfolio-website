<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Taxonomy extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'handle',
        'name',
        'hierarchical',
        'single_select',
    ];

    protected $casts = [
        'hierarchical' => 'boolean',
        'single_select' => 'boolean',
    ];

    public function terms(): HasMany
    {
        return $this->hasMany(Term::class);
    }
}
