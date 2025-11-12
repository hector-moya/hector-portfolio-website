<?php

namespace App\Livewire\Frontend;

use App\Models\Entry;
use Illuminate\Contracts\View\View;
use Illuminate\Contracts\View\Factory;
use Livewire\Attributes\Layout;
use Livewire\Component;

class BlogShow extends Component
{
    public Entry $entry;

    public function mount(string $slug): void
    {
        $this->entry = Entry::query()
            ->where('slug', $slug)
            ->where('status', 'published')
            ->with(['elements.field', 'author', 'collection'])
            ->firstOrFail();
    }

    #[Layout('components.layouts.frontend')]
    public function render(): View|Factory
    {
        return view('livewire.frontend.blog-show');
    }

    public function title(): string
    {
        return $this->entry->title;
    }
}
