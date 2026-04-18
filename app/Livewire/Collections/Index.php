<?php

declare(strict_types=1);

namespace App\Livewire\Collections;

use App\Livewire\Forms\CollectionForm;
use App\Models\Collection as CollectionModel;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

final class Index extends Component
{
    use WithPagination;

    public CollectionForm $form;

    public string $search = '';

    public string $sortBy = '';

    public string $sortDirection = 'asc';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function canReorder(): bool
    {
        return $this->search === '';
    }

    public function reorder(int $id, int $position): void
    {
        if (! $this->canReorder()) {
            return;
        }

        $collections = CollectionModel::query()->orderBy('order')->get();

        $collection = $collections->firstWhere('id', $id);
        $rest = $collections->reject(fn ($c) => $c->id === $id)->values();
        $rest->splice($position, 0, [$collection]);

        foreach ($rest as $index => $c) {
            $c->update(['order' => $index]);
        }

        $this->dispatch('notify', type: 'success', message: __('Order updated.'));
    }

    public function delete(int $id): void
    {
        $this->form->destroy($id);

        $this->dispatch('collection-deleted');
    }

    #[Computed]
    public function collectionModels(): LengthAwarePaginator
    {
        return CollectionModel::query()
            ->with('blueprint')
            ->withCount('entries')
            ->when($this->search, fn ($query) => $query->where('name', 'like', sprintf('%%%s%%', $this->search))
                ->orWhere('slug', 'like', sprintf('%%%s%%', $this->search)))
            ->when($this->sortBy !== '', fn ($query) => $query->orderBy($this->sortBy, $this->sortDirection), fn ($query) => $query->orderBy('order'))
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
    public function render(): View|Factory
    {

        return view('livewire.collections.index');
    }
}
