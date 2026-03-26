<?php

namespace App\Livewire\Navigation;

use App\Facades\Navigation;
use App\Models\Navigation as NavigationModel;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    #[Computed]
    public function navigations()
    {
        return NavigationModel::query()
            ->when($this->search, function ($query): void {
                $query->where('name', 'like', "%{$this->search}%")
                    ->orWhere('handle', 'like', "%{$this->search}%");
            })
            ->latest()
            ->paginate(10);
    }

    public function delete(NavigationModel $navigation): void
    {
        $navigation->delete();
        Navigation::flush();

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => __('Navigation deleted successfully.'),
        ]);
    }

    public function render(): View|Factory
    {
        return view('livewire.navigation.index', [
            'navigations' => $this->navigations,
        ]);
    }
}
