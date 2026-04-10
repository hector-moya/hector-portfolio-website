<?php

declare(strict_types=1);

namespace App\Livewire\ActivityLog;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

final class Index extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'event')]
    public string $eventFilter = '';

    #[Url(as: 'user')]
    public ?int $userFilter = null;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedEventFilter(): void
    {
        $this->resetPage();
    }

    public function updatedUserFilter(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function activities(): LengthAwarePaginator
    {
        return Activity::query()
            ->with('causer')
            ->when($this->search, function ($query): void {
                $query->where('description', 'like', sprintf('%%%s%%', $this->search));
            })
            ->when($this->eventFilter, function ($query): void {
                $query->where('event', $this->eventFilter);
            })
            ->when($this->userFilter, function ($query): void {
                $query->where('causer_id', $this->userFilter)
                    ->where('causer_type', User::class);
            })
            ->latest()
            ->paginate(20);
    }

    #[Computed]
    public function users(): Collection
    {
        return User::query()->orderBy('name')->get();
    }

    #[Title('Activity Log')]
    public function render(): View|Factory
    {
        return view('livewire.activity-log.index');
    }
}
