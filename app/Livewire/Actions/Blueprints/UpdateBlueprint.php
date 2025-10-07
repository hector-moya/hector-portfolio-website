<?php

namespace App\Livewire\Actions\Blueprints;

use App\Models\Blueprint;
use Illuminate\Support\Str;

class UpdateBlueprint
{
    public function execute(array $data = []): Blueprint
    {
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $blueprint = \App\Models\Blueprint::query()->findOrFail($data['id']);

        $blueprint->update([
            'name' => $data['name'],
            'slug' => $data['slug'],
            'description' => $data['description'] ?? null,
            'is_active' => $data['is_active'] ?? false,
        ]);

        $handles = collect($data['elements'])->pluck('handle')->filter();

        // Delete removed elements
        $blueprint->elements()->whereNotIn('handle', $handles)->delete();

        // Update or create remaining
        foreach ($data['elements'] ?? [] as $elementData) {
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
