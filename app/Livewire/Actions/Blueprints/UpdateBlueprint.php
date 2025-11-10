<?php

namespace App\Livewire\Actions\Blueprints;

use App\Models\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class UpdateBlueprint
{
    public function update(array $blueprintData = []): Blueprint
    {

        return DB::transaction(function () use ($blueprintData) {
            $blueprint = Blueprint::query()->findOrFail($blueprintData['id']);

            Gate::authorize('update', $blueprint);

            if (empty($blueprintData['slug'])) {
                $blueprintData['slug'] = Str::slug($blueprintData['name']);
            }

            $blueprint->update([
                'name' => $blueprintData['name'],
                'slug' => $blueprintData['slug'],
                'description' => $blueprintData['description'] ?? null,
                'is_active' => $blueprintData['is_active'] ?? false,
            ]);

            return $blueprint->fresh('fields');
        }
        );
    }
}
