<?php

namespace App\Livewire\Actions\Blueprints;

use App\Models\Blueprint;
use Illuminate\Support\Str;

class UpdateBlueprint
{
    public function update(array $blueprintData = []): Blueprint
    {
        if (empty($blueprintData['slug'])) {
            $blueprintData['slug'] = Str::slug($blueprintData['name']);
        }

        $blueprint = Blueprint::query()->findOrFail($blueprintData['id']);

        $blueprint->update([
            'name' => $blueprintData['name'],
            'slug' => $blueprintData['slug'],
            'description' => $blueprintData['description'] ?? null,
            'is_active' => $blueprintData['is_active'] ?? false,
        ]);

        $handles = collect($blueprintData['elements'])->pluck('handle')->filter();

        // Delete removed elements
        $blueprint->elements()->whereNotIn('handle', $handles)->delete();

        // Update or create remaining
        foreach ($blueprintData['elements'] ?? [] as $elementData) {
            $blueprint->elements()->updateOrCreate(
                ['handle' => $elementData['handle']],
                [
                    'type' => $elementData['type'],
                    'label' => $elementData['label'],
                    'instructions' => $elementData['instructions'] ?? null,
                    'is_required' => $elementData['is_required'] ?? false,
                    'config' => $elementData['config'] ?? [],
                ]
            );
        }

        return $blueprint->fresh('elements');
    }
}
