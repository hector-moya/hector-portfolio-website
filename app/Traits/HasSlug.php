<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasSlug
{
    public function generateSlug(string $slug): string
    {
        return Str::slug($slug);
    }
}
