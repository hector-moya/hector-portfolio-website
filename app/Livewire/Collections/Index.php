<?php

namespace App\Livewire\Collections;

use App\Livewire\Actions\Collections\DeleteCollection;
use App\Models\Collection as CollectionModel;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public string $sortBy = 'date';

    public string $sortDirection = 'desc';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function delete(int $id): void
    {
        $collection = CollectionModel::query()->findOrFail($id);

        (new DeleteCollection)->execute($collection);

        $this->dispatch('collection-deleted');
    }

    #[Computed]
    public function collectionModels(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return CollectionModel::query()
            ->with('blueprint')
            ->withCount('entries')
            ->when($this->search, fn ($query) => $query->where('name', 'like', "%{$this->search}%")
                ->orWhere('slug', 'like', "%{$this->search}%"))
            ->tap(fn ($query) => $this->sortBy !== '' && $this->sortBy !== '0' ? $query->orderBy($this->sortBy, $this->sortDirection) : $query)
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

    #[Title('Collections')]
    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {

        return view('livewire.collections.index');
    }
}
