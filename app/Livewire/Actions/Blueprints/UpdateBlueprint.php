<?php

declare(strict_types=1);

namespace App\Livewire\Actions\Blueprints;

use App\Models\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

final class UpdateBlueprint
{
    public function update(Blueprint $blueprint, array $blueprintData = []): Blueprint
    {
        Gate::authorize('update', $blueprint);

        return DB::transaction(function () use ($blueprint, $blueprintData) {
            if (empty($blueprintData['slug'])) {
                $blueprintData['slug'] = Str::slug($blueprintData['name']);
            }

            $blueprint->update([
                'name' => $blueprintData['name'],
                'slug' => $blueprintData['slug'],
                'description' => $blueprintData['description'] ?? null,
                'is_active' => $blueprintData['is_active'] ?? false,
                'settings' => array_merge($blueprint->settings ?? [], $blueprintData['settings'] ?? []),
            ]);

            $blueprint->load('fields');

            return $blueprint;
        });
    }
}
