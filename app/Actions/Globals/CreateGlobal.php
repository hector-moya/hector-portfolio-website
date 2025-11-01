<?php

declare(strict_types=1);

namespace App\Actions\Globals;

use App\Models\GlobalSet;
use Illuminate\Support\Facades\Gate;

class CreateGlobal
{
    public function handle(array $globalData): GlobalSet
    {
        Gate::authorize('create', GlobalSet::class);

        return GlobalSet::query()->create([
            'name' => $globalData['name'],
            'handle' => $globalData['handle'],
            'blueprint_id' => $globalData['blueprint_id'],
        ]);
    }
}
