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
    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        $collection = \App\Models\Collection::query()->where('slug', 'portfolio')->first();

        $projects = \App\Models\Entry::query()->where('collection_id', $collection?->id)
            ->where('status', 'published')
            ->with(['elements.blueprintElement'])
            ->latest('published_at')
            ->get();

        return view('livewire.frontend.portfolio-index', [
            'projects' => $projects,
        ]);
    }
}
