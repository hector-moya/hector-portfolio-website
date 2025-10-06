<?php

namespace App\Livewire\Frontend;

use App\Models\Collection;
use App\Models\Entry;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class PortfolioIndex extends Component
{
    #[Layout('components.layouts.frontend')]
    #[Title('Portfolio')]
    public function render()
    {
        $collection = Collection::where('slug', 'portfolio')->first();

        $projects = Entry::where('collection_id', $collection?->id)
            ->where('status', 'published')
            ->with(['elements.blueprintElement'])
            ->latest('published_at')
            ->get();

        return view('livewire.frontend.portfolio-index', [
            'projects' => $projects,
        ]);
    }
}
