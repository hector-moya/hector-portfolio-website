<?php

declare(strict_types=1);

namespace App\Livewire\Globals;

use App\Models\GlobalSet;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    #[Computed]
    public function globalSets(): LengthAwarePaginator
    {
        return GlobalSet::with('blueprint')
            ->latest()
            ->paginate(10);
    }

    public function render(): View
    {
        return view('livewire.globals.index');
    }
}
