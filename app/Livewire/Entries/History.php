<?php

declare(strict_types=1);

namespace App\Livewire\Entries;

use App\Models\Activity;
use App\Models\Entry;
use App\Models\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

final class History extends Component
{
    public ?int $entryId = null;

    #[On('open-history')]
    public function open(int $entryId): void
    {
        $this->entryId = $entryId;
    }

    public function restore(int $activityId): void
    {
        $activity = Activity::query()->findOrFail($activityId);

        if ($activity->subject_type !== Entry::class || $activity->subject_id !== $this->entryId) {
            return;
        }

        $oldValues = $activity->properties['old'] ?? [];
        if (empty($oldValues)) {
            return;
        }

        $entry = Entry::query()->findOrFail($this->entryId);

        $fillable = array_intersect_key($oldValues, array_flip(['title', 'slug', 'status', 'seo_title', 'seo_description', 'og_image']));

        if ($fillable !== []) {
            $entry->update($fillable);

            Activity::query()->create([
                'log_name' => 'entry',
                'description' => 'Restored entry to previous version',
                'subject_type' => Entry::class,
                'subject_id' => $entry->id,
                'causer_type' => User::class,
                'causer_id' => auth()->id(),
                'event' => 'updated',
                'properties' => [
                    'old' => ['title' => $entry->title, 'slug' => $entry->slug, 'status' => $entry->status],
                    'new' => $fillable,
                ],
            ]);
        }

        $this->dispatch('entry-restored');
        $this->dispatch('notify', message: 'Entry restored to previous version.');
    }

    #[Computed]
    public function revisions(): Collection
    {
        if ($this->entryId === null) {
            return new Collection;
        }

        return Activity::query()
            ->where('subject_type', Entry::class)
            ->where('subject_id', $this->entryId)
            ->with('causer')
            ->latest()
            ->get();
    }

    public function render(): View|Factory
    {
        return view('livewire.entries.history');
    }
}
