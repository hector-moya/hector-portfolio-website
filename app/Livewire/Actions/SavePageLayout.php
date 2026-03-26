<?php

namespace App\Livewire\Actions;

use App\Models\Entry;
use Illuminate\Support\Facades\DB;

class SavePageLayout
{
    /**
     * @param  array<int, array{_id: string, type: string, data: array<string, mixed>}>  $sections
     */
    public function handle(Entry $entry, array $sections): Entry
    {
        return DB::transaction(function () use ($entry, $sections) {
            $entry->update(['layout' => $sections]);

            return $entry->fresh();
        });
    }
}
