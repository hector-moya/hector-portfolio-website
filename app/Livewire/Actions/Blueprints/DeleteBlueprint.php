<?php

namespace App\Livewire\Actions\Blueprints;

use App\Models\Blueprint;
use Illuminate\Support\Facades\Gate;

class DeleteBlueprint
{
    public function execute(Blueprint $blueprint): bool
    {
        Gate::authorize('delete', $blueprint);

        return $blueprint->delete();
    }
}
