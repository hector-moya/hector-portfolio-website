<?php

namespace App\Livewire\Actions\Blueprints;

use App\Models\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class CreateBlueprint
{
    public function create(array $blueprintData): Blueprint
    {
        // Gate::authorize('create', Blueprint::class);

        return DB::transaction(function () use ($blueprintData) {
            if (empty($blueprintData['slug'])) {
                $blueprintData['slug'] = Str::slug($blueprintData['name']);
            }

            $blueprint = Blueprint::query()->create([
                'name' => $blueprintData['name'],
                'slug' => $blueprintData['slug'],
                'description' => $blueprintData['description'] ?? null,
                'is_active' => $blueprintData['is_active'] ?? true,
            ]);

            // Create tabs, sections, and fields
            foreach ($blueprintData['tabs'] as $tabIndex => $tab) {
                $createdTab = $blueprint->tabs()->create([
                    'name' => $tab['name'],
                    'handle' => $tab['handle'],
                    'sort_order' => $tab['sortOrder'] ?? $tabIndex,
                ]);

                foreach ($tab['sections'] as $sectionIndex => $section) {
                    $createdSection = $createdTab->sections()->create([
                        'name' => $section['name'],
                        'handle' => $section['handle'],
                        'blueprint_id' => $blueprint->id,
                        'instructions' => $section['instructions'],
                        'sort_order' => $section['sortOrder'] ?? $sectionIndex,
                    ]);

                    foreach ($section['fields'] as $fieldIndex => $field) {
                        $createdSection->fields()->create([
                            'label' => $field['label'],
                            'blueprint_id' => $blueprint->id,
                            'section_id' => $createdSection->id,
                            'handle' => $field['handle'],
                            'type' => $field['type'],
                            'instructions' => $field['instructions'],
                            'is_required' => $field['is_required'],
                            'config' => $field['config'],
                            'order' => $field['sortOrder'] ?? $fieldIndex,
                        ]);
                    }
                }
            }

            return $blueprint->fresh(['tabs.sections.fields']);

        });
    }
}
