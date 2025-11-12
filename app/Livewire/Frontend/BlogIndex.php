<?php

namespace App\Livewire\Frontend;

use App\Models\Collection as CollectionModel;
use App\Models\Entry;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class BlogIndex extends Component
{
    use WithPagination;

    #[Layout('components.layouts.frontend')]
    #[Title('Blog')]
    public function render(): View|Factory
    {
        $collection = CollectionModel::query()->where('slug', 'blog')->first();

        $posts = Entry::query()
            ->whereHas('collection', function ($query) use ($collection) {
                $query->where('id', $collection?->id);
            })
            ->where('status', 'published')
            ->with(['elements.field', 'author'])
            ->latest('published_at')
            ->paginate(9);

        return view('livewire.frontend.blog-index', [
            'posts' => $posts,
        ]);
    }
}
