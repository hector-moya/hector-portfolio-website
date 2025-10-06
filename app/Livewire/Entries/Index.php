<?php

namespace App\Livewire\Entries;

use App\Livewire\Actions\DeleteEntry;
use App\Models\Collection;
use App\Models\Entry;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'collection')]
    public ?int $collectionFilter = null;

    #[Url(as: 'status')]
    public ?string $statusFilter = null;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedCollectionFilter(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function entries()
    {
        return Entry::query()
            ->with(['collection', 'blueprint', 'author'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', "%{$this->search}%")
                        ->orWhere('slug', 'like', "%{$this->search}%");
                });
            })
            ->when($this->collectionFilter, function ($query) {
                $query->where('collection_id', $this->collectionFilter);
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->latest()
            ->paginate(15);
    }

    #[Computed]
    public function collections()
    {
        return Collection::query()
            ->withCount('entries')
            ->orderBy('title')
            ->get();
    }

    #[On('entry-deleted')]
    public function entryDeleted(): void
    {
        $this->resetPage();
    }

    public function delete(int $id): void
    {
        $entry = Entry::findOrFail($id);

        (new DeleteEntry)->execute($entry);

        $this->dispatch('entry-deleted');
        $this->dispatch('notify', message: 'Entry deleted successfully.');
    }

    public function render()
    {
        return view('livewire.entries.index');
    }
}
