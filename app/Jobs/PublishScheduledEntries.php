<?php

namespace App\Jobs;

use App\Models\Activity;
use App\Models\Entry;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class PublishScheduledEntries implements ShouldQueue
{
    use Queueable;

    public function __construct() {}

    public function handle(): void
    {
        $entries = Entry::query()
            ->where('status', 'draft')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->get();

        foreach ($entries as $entry) {
            $entry->update(['status' => 'published']);

            Activity::query()->create([
                'log_name' => 'entry',
                'description' => 'Auto-published scheduled entry',
                'subject_type' => Entry::class,
                'subject_id' => $entry->id,
                'causer_type' => User::class,
                'causer_id' => $entry->author_id,
                'event' => 'updated',
                'properties' => [
                    'old' => ['status' => 'draft'],
                    'new' => ['status' => 'published'],
                ],
            ]);
        }
    }
}
