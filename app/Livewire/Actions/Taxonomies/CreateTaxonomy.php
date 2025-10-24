<?php

namespace App\Livewire\Actions\Taxonomies;

use App\Models\Activity;
use App\Models\Taxonomy;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CreateTaxonomy
{
    public function create(array $taxonomyData): Taxonomy
    {
        return DB::transaction(function () use ($taxonomyData) {
            // Create the taxonomy
            $taxonomy = Taxonomy::query()->create([
                'name' => $taxonomyData['name'],
                'handle' => $taxonomyData['handle'],
                'hierarchical' => $taxonomyData['hierarchical'],
                'single_select' => $taxonomyData['single_select'],
            ]);

            // Log activity
            Activity::query()->create([
                'log_name' => 'taxonomy',
                'description' => 'Created taxonomy',
                'subject_type' => Taxonomy::class,
                'subject_id' => $taxonomy->id,
                'causer_type' => User::class,
                'causer_id' => auth()->id(),
                'event' => 'created',
                'properties' => [
                    'name' => $taxonomy->name,
                    'handle' => $taxonomy->handle,
                ],
            ]);

            return $taxonomy;
        });
    }
}
