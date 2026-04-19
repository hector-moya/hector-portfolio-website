<?php

declare(strict_types=1);

namespace App\Livewire\Blueprints;

use App\Livewire\Forms\BlueprintForm;
use App\Models\Blueprint;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

final class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public BlueprintForm $blueprintForm;

    public string $sortBy = '';

    public string $sortDirection = 'asc';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function createBlueprint(): void
    {
        $blueprint = $this->blueprintForm->create();
        $this->redirectRoute('blueprints.create', $blueprint);
    }

    public function delete(int $blueprintId): void
    {
        $this->blueprintForm->destroy($blueprintId);
        $this->dispatch('blueprint-deleted');
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

        $blueprints = Blueprint::query()->orderBy('order')->get();

        $blueprint = $blueprints->firstWhere('id', $id);
        $rest = $blueprints->reject(fn ($b): bool => $b->id === $id)->values();
        $rest->splice($position, 0, [$blueprint]);

        foreach ($rest as $index => $b) {
            $b->update(['order' => $index]);
        }

        $this->dispatch('notify', type: 'success', message: __('Order updated.'));
    }

    #[Computed]
    public function blueprints(): LengthAwarePaginator
    {
        return Blueprint::query()
            ->withCount('fields', 'collections')
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

    #[Title('Blueprints')]
    public function render(): View|Factory
    {
        return view('livewire.blueprints.index');
    }
}
