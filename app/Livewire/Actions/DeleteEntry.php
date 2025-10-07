<?php

namespace App\Livewire\Actions;

use App\Models\Activity;
use App\Models\Entry;

class DeleteEntry
{
    public function execute(Entry $entry): bool
    {
        // Log activity before deletion
        \App\Models\Activity::query()->create([
            'log_name' => 'entry',
            'description' => 'Deleted entry',
            'subject_type' => Entry::class,
            'subject_id' => $entry->id,
            'causer_type' => \App\Models\User::class,
            'causer_id' => auth()->id(),
            'event' => 'deleted',
            'properties' => [
                'title' => $entry->title,
                'slug' => $entry->slug,
            ],
        ]);

        return $entry->delete();
    }
}
