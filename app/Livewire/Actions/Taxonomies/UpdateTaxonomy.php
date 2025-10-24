<?php

namespace App\Livewire\Actions\Taxonomies;

use App\Models\Activity;
use App\Models\Taxonomy;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UpdateTaxonomy
{
    public function update(array $taxonomyData): Taxonomy
    {
        return DB::transaction(function () use ($taxonomyData) {
            $taxonomy = Taxonomy::query()->findOrFail($taxonomyData['id']);

            // Store old values for logging
            $oldValues = [
                'name' => $taxonomy->name,
                'handle' => $taxonomy->handle,
                'hierarchical' => $taxonomy->hierarchical,
                'single_select' => $taxonomy->single_select,
            ];

            // Update the taxonomy
            $taxonomy->update([
                'name' => $taxonomyData['name'],
                'handle' => $taxonomyData['handle'],
                'hierarchical' => $taxonomyData['hierarchical'],
                'single_select' => $taxonomyData['single_select'],
            ]);

            // Log activity
            Activity::query()->create([
                'log_name' => 'taxonomy',
                'description' => 'Updated taxonomy',
                'subject_type' => Taxonomy::class,
                'subject_id' => $taxonomy->id,
                'causer_type' => User::class,
                'causer_id' => auth()->id(),
                'event' => 'updated',
                'properties' => [
                    'old' => $oldValues,
                    'new' => [
                        'name' => $taxonomy->name,
                        'handle' => $taxonomy->handle,
                        'hierarchical' => $taxonomy->hierarchical,
                        'single_select' => $taxonomy->single_select,
                    ],
                ],
            ]);

            return $taxonomy->fresh();
        });
    }
}
