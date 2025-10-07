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

        $blueprint = Blueprint::findOrFail($data['id']);

        $blueprint->update([
            'name' => $data['name'],
            'slug' => $data['slug'],
            'description' => $data['description'] ?? null,
            'is_active' => $data['is_active'] ?? false,
        ]);

        //Sync elements create, update or delete as necessary
        $existingElementIds = $blueprint->elements->pluck('id')->toArray();
        $submittedElementIds = collect($data['elements'] ?? [])->pluck('id')->toArray();
        $elementsToDelete = array_diff($existingElementIds, $submittedElementIds);
        if (!empty($elementsToDelete)) {
            $blueprint->elements()->whereIn('id', $elementsToDelete)->delete();
        }

        foreach ($data['elements'] ?? [] as $elementData) {
            if (isset($elementData['id']) && in_array($elementData['id'], $existingElementIds)) {
                // Update existing element
                $element = $blueprint->elements()->where('id', $elementData['id'])->first();
                $element->update([
                    'label' => $elementData['label'],
                    'handle' => Str::slug($elementData['handle'], '_'),
                    'type' => $elementData['type'],
                    'config' => $elementData['config'] ?? [],
                    'is_required' => $elementData['is_required'] ?? false,
                    'order' => $elementData['order'] ?? 0,
                ]);
            } else {
                // Create new element
                $blueprint->elements()->create([
                    'label' => $elementData['label'],
                    'handle' => Str::slug($elementData['handle'], '_'),
                    'type' => $elementData['type'],
                    'config' => $elementData['config'] ?? [],
                    'is_required' => $elementData['is_required'] ?? false,
                    'order' => $elementData['order'] ?? 0,
                ]);
            }
        }

        return $blueprint->fresh('elements');
    }
}
