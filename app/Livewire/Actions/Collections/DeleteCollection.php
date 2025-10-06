<?php

namespace App\Livewire\Actions\Collections;

use App\Models\Collection;

class DeleteCollection
{
    public function execute(Collection $collection): bool
    {
        return $collection->delete();
    }
}
