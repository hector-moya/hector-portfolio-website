<?php

declare(strict_types=1);

namespace App\Livewire\Actions;

use App\Models\Activity;
use App\Models\Entry;
use App\Models\User;

final class DeleteEntry
{
    public function execute(Entry $entry): bool
    {
        // Log activity before deletion
        Activity::query()->create([
            'log_name' => 'entry',
            'description' => 'Deleted entry',
            'subject_type' => Entry::class,
            'subject_id' => $entry->id,
            'causer_type' => User::class,
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
