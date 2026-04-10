<?php

declare(strict_types=1);

namespace App\Livewire\Actions\Blueprints;

use App\Models\Blueprint;
use Illuminate\Support\Facades\Gate;

final class DeleteBlueprint
{
    public function execute(Blueprint $blueprint): bool
    {
        Gate::authorize('delete', $blueprint);

        return $blueprint->delete();
    }
}
