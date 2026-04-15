<?php

declare(strict_types=1);

namespace App\Livewire\Actions\Collections;

use App\Models\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

final class UpdateCollection
{
    public function execute(Collection $collection, array $data): Collection
    {
        Gate::authorize('update', $collection);
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $collection->update($data);

        return $collection->fresh();
    }
}
