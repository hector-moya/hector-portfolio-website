<?php

namespace App\Livewire\Actions\Collections;

use App\Models\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class CreateCollection
{
    public function execute(array $data): Collection
    {
        Gate::authorize('create', Collection::class);

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        return Collection::query()->create($data);
    }
}
