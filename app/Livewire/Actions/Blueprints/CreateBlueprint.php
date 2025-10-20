<?php

namespace App\Livewire\Actions\Blueprints;

use App\Models\Blueprint;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class CreateBlueprint
{
    public function create(array $blueprintData, array $elements = []): Blueprint
    {
        Gate::authorize('create', Blueprint::class);

        if (empty($blueprintData['slug'])) {
            $blueprintData['slug'] = Str::slug($blueprintData['name']);
        }

        $blueprint = Blueprint::query()->create($blueprintData);

        // Create elements if provided
        foreach ($elements as $index => $element) {
            $blueprint->elements()->create([
                'type' => $element['type'],
                'label' => $element['label'],
                'handle' => $element['handle'] ?? Str::slug($element['label'], '_'),
                'instructions' => $element['instructions'] ?? null,
                'config' => $element['config'] ?? [],
                'is_required' => $element['is_required'] ?? false,
                'order' => $index,
            ]);
        }

        return $blueprint->fresh('elements');
    }
}
