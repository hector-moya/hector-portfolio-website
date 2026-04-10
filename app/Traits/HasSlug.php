<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Support\Str;

trait HasSlug
{
    public function generateSlug(string $slug): string
    {
        return Str::slug($slug);
    }
}
