<?php

namespace App\Livewire;

use App\Models\Activity;
use App\Models\Asset;
use App\Models\Collection;
use App\Models\Entry;
use App\Models\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Dashboard extends Component
{
    #[Computed]
    public function stats(): array
    {
        return [
            'total_entries' => Entry::query()->count(),
            'published_entries' => Entry::query()->where('status', 'published')->count(),
            'draft_entries' => Entry::query()->where('status', 'draft')->count(),
            'archived_entries' => Entry::query()->where('status', 'archived')->count(),
            'total_collections' => Collection::query()->count(),
            'total_assets' => Asset::query()->count(),
            'total_users' => User::query()->count(),
        ];
    }

    #[Computed]
    public function recentActivity(): \Illuminate\Database\Eloquent\Collection
    {
        return Activity::query()
            ->with('causer')
            ->latest()
            ->limit(10)
            ->get();
    }

    #[Computed]
    public function recentEntries(): \Illuminate\Database\Eloquent\Collection
    {
        return Entry::query()
            ->with(['collection', 'author'])
            ->latest()
            ->limit(5)
            ->get();
    }

    #[Title('Dashboard')]
    public function render(): View|Factory
    {
        return view('livewire.dashboard');
    }
}
