<?php

declare(strict_types=1);

namespace App\Livewire\Actions\Collections;

use App\Models\Collection;
use Illuminate\Support\Facades\Gate;

final class DeleteCollection
{
    public function execute(Collection $collection): bool
    {
        Gate::authorize('delete', $collection);

        return $collection->delete();
    }
}
