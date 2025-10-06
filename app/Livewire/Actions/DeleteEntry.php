<?php

namespace App\Livewire\Actions;

use App\Models\Entry;

class DeleteEntry
{
    public function execute(Entry $entry): bool
    {
        return (bool) $entry->delete();
    }
}
