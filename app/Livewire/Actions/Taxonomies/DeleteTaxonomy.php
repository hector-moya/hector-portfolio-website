<?php

declare(strict_types=1);

namespace App\Livewire\Actions\Taxonomies;

use App\Models\Activity;
use App\Models\Taxonomy;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final class DeleteTaxonomy
{
    public function execute(Taxonomy $taxonomy): void
    {
        DB::transaction(function () use ($taxonomy): void {
            $taxonomy->delete();

            Activity::query()->create([
                'log_name' => 'taxonomy',
                'description' => 'Deleted taxonomy',
                'subject_type' => Taxonomy::class,
                'subject_id' => $taxonomy->id,
                'causer_type' => User::class,
                'causer_id' => auth()->id(),
                'event' => 'deleted',
                'properties' => [
                    'name' => $taxonomy->name,
                    'handle' => $taxonomy->handle,
                ],
            ]);
        });
    }
}
