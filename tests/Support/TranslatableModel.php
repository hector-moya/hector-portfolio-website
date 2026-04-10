<?php

declare(strict_types=1);

namespace Tests\Support;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Model;

final class TranslatableModel extends Model
{
    use HasFactory;
    use HasTranslations;

    protected $fillable = ['title'];

    protected $table = 'entries';
}
