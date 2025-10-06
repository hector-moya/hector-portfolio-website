<?php

namespace App\Livewire\Collections;

use App\Livewire\Actions\Collections\DeleteCollection;
use App\Models\Collection;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function delete(int $id): void
    {
        $collection = Collection::findOrFail($id);

        (new DeleteCollection)->execute($collection);

        $this->dispatch('collection-deleted');
    }

    #[Title('Collections')]
    public function render()
    {
        $collections = Collection::query()
            ->with('blueprint')
            ->when($this->search, fn ($query) => $query->where('name', 'like', "%{$this->search}%")
                ->orWhere('slug', 'like', "%{$this->search}%"))
            ->latest()
            ->paginate(10);

        return view('livewire.collections.index', [
            'collections' => $collections,
        ]);
    }
}
