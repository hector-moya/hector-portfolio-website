<?php

namespace App\Livewire\Actions\Blueprints;

use App\Models\Blueprint;
use Illuminate\Support\Str;

class CreateBlueprint
{
    public function execute(array $data, array $elements = []): Blueprint
    {
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $blueprint = \App\Models\Blueprint::query()->create($data);

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
