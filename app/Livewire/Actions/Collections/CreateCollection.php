<?php

namespace App\Livewire\Actions\Collections;

use App\Models\Collection;
use Illuminate\Support\Str;

class CreateCollection
{
    public function execute(array $data): Collection
    {
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        return \App\Models\Collection::query()->create($data);
    }
}
