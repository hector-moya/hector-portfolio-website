<?php

namespace App\Livewire\Entries;

use App\Livewire\Actions\DeleteEntry;
use App\Models\Collection as ModelsCollection;
use App\Models\Entry;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
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

    public array $selected = [];

    public bool $selectAll = false;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedSelectAll(): void
    {
        $entries = $this->entries();
        $this->selected = $this->selectAll ? $entries->pluck('id')->toArray() : [];
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
            ->when($this->search, function ($query): void {
                $query->where(function ($q): void {
                    $q->where('title', 'like', "%{$this->search}%")
                        ->orWhere('slug', 'like', "%{$this->search}%");
                });
            })
            ->when($this->collectionFilter, function ($query): void {
                $query->where('collection_id', $this->collectionFilter);
            })
            ->when($this->statusFilter, function ($query): void {
                $query->where('status', $this->statusFilter);
            })
            ->latest()
            ->paginate(15);
    }

    #[Computed]
    public function collections()
    {
        return ModelsCollection::query()
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
        $entry = \App\Models\Entry::query()->findOrFail($id);

        (new DeleteEntry)->execute($entry);

        $this->dispatch('entry-deleted');
        $this->dispatch('notify', message: 'Entry deleted successfully.');
    }

    public function bulkPublish(): void
    {
        if ($this->selected === []) {
            return;
        }

        \App\Models\Entry::query()->whereIn('id', $this->selected)->update([
            'status' => 'published',
            'published_at' => now(),
        ]);

        $count = count($this->selected);
        $this->selected = [];
        $this->selectAll = false;

        $this->dispatch('notify', message: "{$count} entries published successfully.");
    }

    public function bulkUnpublish(): void
    {
        if ($this->selected === []) {
            return;
        }

        \App\Models\Entry::query()->whereIn('id', $this->selected)->update([
            'status' => 'draft',
        ]);

        $count = count($this->selected);
        $this->selected = [];
        $this->selectAll = false;

        $this->dispatch('notify', message: "{$count} entries unpublished successfully.");
    }

    public function bulkDelete(): void
    {
        if ($this->selected === []) {
            return;
        }

        \App\Models\Entry::query()->whereIn('id', $this->selected)->delete();

        $count = count($this->selected);
        $this->selected = [];
        $this->selectAll = false;

        $this->dispatch('entry-deleted');
        $this->dispatch('notify', message: "{$count} entries deleted successfully.");
    }

    #[Title('Entries')]
    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.entries.index');
    }
}
