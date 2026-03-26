<?php

declare(strict_types=1);

namespace App\Actions\Globals;

use App\Models\GlobalSet;
use App\Models\GlobalVariable;
use Illuminate\Support\Facades\Gate;

class UpdateGlobal
{
    public function handle(array $globalData): GlobalSet
    {
        $globalSet = GlobalSet::query()->findOrFail($globalData['id']);

        Gate::authorize('update', $globalSet);

        $globalSet->update([
            'name' => $globalData['name'],
            'handle' => $globalData['handle'],
            'blueprint_id' => $globalData['blueprint_id'],
        ]);

        foreach ($globalData['variables'] ?? [] as $handle => $value) {
            GlobalVariable::query()->updateOrCreate(
                ['global_set_id' => $globalSet->id, 'handle' => $handle],
                ['value' => $value]
            );
        }

        return $globalSet->fresh();
    }
}
