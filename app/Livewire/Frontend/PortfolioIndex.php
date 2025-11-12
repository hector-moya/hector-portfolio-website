<?php

namespace App\Livewire\Frontend;

use App\Models\Collection as CollectionModel;
use App\Models\Entry;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class PortfolioIndex extends Component
{
    #[Layout('components.layouts.frontend')]
    #[Title('Portfolio')]
    public function render(): View|Factory
    {
        $collection = CollectionModel::query()->where('slug', 'portfolio')->first();

        $projects = Entry::query()
            ->whereHas('collection', function ($query) use ($collection): void {
                $query->where('id', $collection?->id);
            })
            ->where('status', 'published')
            ->with(['elements.field'])
            ->latest('published_at')
            ->get();

        return view('livewire.frontend.portfolio-index', [
            'projects' => $projects,
        ]);
    }
}
