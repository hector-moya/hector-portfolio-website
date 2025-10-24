<?php

namespace App\Livewire\Taxonomies;

use App\Livewire\Forms\TaxonomyForm;
use App\Models\Taxonomy;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public TaxonomyForm $form;

    public string $search = '';

    public string $sortBy = 'date';

    public string $sortDirection = 'desc';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function delete(int $taxonomyId): void
    {
        $this->form->destroy($taxonomyId);
        $this->dispatch('taxonomy-deleted');
    }

    #[Computed]
    public function taxonomies(): LengthAwarePaginator
    {
        return Taxonomy::query()
            ->when($this->search, fn ($query) => $query->where('name', 'like', "%{$this->search}%"))
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

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.taxonomies.index');
    }
}
