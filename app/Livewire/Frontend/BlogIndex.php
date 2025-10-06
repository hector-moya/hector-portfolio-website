<?php

namespace App\Livewire\Frontend;

use App\Models\Collection;
use App\Models\Entry;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class BlogIndex extends Component
{
    use WithPagination;

    #[Layout('components.layouts.frontend')]
    #[Title('Blog')]
    public function render()
    {
        $collection = Collection::where('slug', 'blog')->first();

        $posts = Entry::where('collection_id', $collection?->id)
            ->where('status', 'published')
            ->with(['elements.blueprintElement', 'author'])
            ->latest('published_at')
            ->paginate(9);

        return view('livewire.frontend.blog-index', [
            'posts' => $posts,
        ]);
    }
}
