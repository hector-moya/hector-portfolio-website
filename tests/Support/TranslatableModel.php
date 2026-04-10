<?php

declare(strict_types=1);

namespace Tests\Support;

use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Model;

final class TranslatableModel extends Model
{
    use HasTranslations;

    protected $fillable = ['title'];

    protected $table = 'entries';
}
