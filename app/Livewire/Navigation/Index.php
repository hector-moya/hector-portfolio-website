<?php

namespace App\Livewire\Navigation;

use App\Facades\Navigation;
use App\Models\Navigation as NavigationModel;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public function getNavigationsProperty()
    {
        return NavigationModel::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', "%{$this->search}%")
                    ->orWhere('handle', 'like', "%{$this->search}%");
            })
            ->latest()
            ->paginate(10);
    }

    public function delete(NavigationModel $navigation)
    {
        $navigation->delete();
        Navigation::flush();

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => __('Navigation deleted successfully.'),
        ]);
    }

    public function render()
    {
        return view('livewire.navigation.index', [
            'navigations' => $this->navigations,
        ]);
    }
}
