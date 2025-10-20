<?php

namespace App\Livewire\Blueprints;

use App\Models\Blueprint;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Livewire\Forms\BlueprintForm;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public BlueprintForm $blueprintForm;

    public string $sortBy = 'date';
    public string $sortDirection = 'desc';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function delete(int $blueprintId): void
    {
        $this->blueprintForm->destroy($blueprintId);
    }

    #[Computed]
    public function blueprints(): LengthAwarePaginator
    {
        return Blueprint::query()
            ->withCount('elements', 'collections')
            ->when($this->search, fn ($query) => $query->where('name', 'like', "%{$this->search}%")
                ->orWhere('slug', 'like', "%{$this->search}%"))
            ->tap(fn ($query) => $this->sortBy ? $query->orderBy($this->sortBy, $this->sortDirection) : $query)
            ->latest()
            ->paginate(10);
    }

    public function sort(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    #[Title('Blueprints')]
    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.blueprints.index');
    }
}
