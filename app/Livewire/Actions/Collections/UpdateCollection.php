<?php

namespace App\Livewire\Actions\Collections;

use App\Models\Collection;
use Illuminate\Support\Str;

class UpdateCollection
{
    public function execute(Collection $collection, array $data): Collection
    {
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $collection->update($data);

        return $collection->fresh();
    }
}
