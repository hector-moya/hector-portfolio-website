<?php

namespace App\Livewire\Frontend;

use App\Models\Entry;
use Livewire\Attributes\Layout;
use Livewire\Component;

class BlogShow extends Component
{
    public Entry $entry;

    public function mount(string $slug)
    {
        $this->entry = Entry::where('slug', $slug)
            ->where('status', 'published')
            ->with(['elements.blueprintElement', 'author', 'collection'])
            ->firstOrFail();
    }

    #[Layout('components.layouts.frontend')]
    public function render()
    {
        return view('livewire.frontend.blog-show');
    }

    public function title(): string
    {
        return $this->entry->title;
    }
}
