<?php

declare(strict_types=1);

namespace App\Livewire\Actions\Collections;

use App\Models\Collection;

final class DeleteCollection
{
    public function execute(Collection $collection): bool
    {
        return $collection->delete();
    }
}
