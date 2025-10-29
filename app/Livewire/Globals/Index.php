<?php

declare(strict_types=1);

namespace App\Livewire\Globals;

use App\Models\GlobalSet;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public function render(): View
    {
        return view('livewire.globals.index', [
            'globalSets' => GlobalSet::with('blueprint')
                ->latest()
                ->paginate(10),
        ]);
    }
}
