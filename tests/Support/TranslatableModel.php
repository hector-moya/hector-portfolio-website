<?php

namespace Tests\Support;

use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Model;

class TranslatableModel extends Model
{
    use HasTranslations;

    protected $fillable = ['title'];

    protected $table = 'entries';
}
