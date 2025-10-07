<?php

namespace App\Livewire\Blueprints;

use App\Livewire\Actions\Blueprints\DeleteBlueprint;
use App\Models\Blueprint;
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
        $blueprint = \App\Models\Blueprint::query()->findOrFail($id);

        (new DeleteBlueprint)->execute($blueprint);

        $this->dispatch('blueprint-deleted');
    }

    #[Title('Blueprints')]
    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        $blueprints = Blueprint::query()
            ->withCount('elements', 'collections')
            ->when($this->search, fn ($query) => $query->where('name', 'like', "%{$this->search}%")
                ->orWhere('slug', 'like', "%{$this->search}%"))
            ->latest()
            ->paginate(10);

        return view('livewire.blueprints.index', [
            'blueprints' => $blueprints,
        ]);
    }
}
