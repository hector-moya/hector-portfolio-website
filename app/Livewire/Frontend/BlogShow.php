<?php

namespace App\Livewire\Frontend;

use App\Models\Entry;
use Livewire\Attributes\Layout;
use Livewire\Component;

class BlogShow extends Component
{
    public Entry $entry;

    public function mount(string $slug): void
    {
        $this->entry = \App\Models\Entry::query()->where('slug', $slug)
            ->where('status', 'published')
            ->with(['elements.blueprintElement', 'author', 'collection'])
            ->firstOrFail();
    }

    #[Layout('components.layouts.frontend')]
    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.frontend.blog-show');
    }

    public function title(): string
    {
        return $this->entry->title;
    }
}
