<?php

declare(strict_types=1);

namespace App\Livewire\Actions\Collections;

use App\Models\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

final class CreateCollection
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
