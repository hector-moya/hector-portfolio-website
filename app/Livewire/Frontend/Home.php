<?php

namespace App\Livewire\Frontend;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Contracts\View\View;
use Illuminate\Contracts\View\Factory;
use App\Models\Entry;
use Livewire\Component;

class Home extends Component
{
    #[Layout('components.layouts.frontend')]
    #[Title('Home')]
    public function render(): View|Factory
    {
        $entry = Entry::query()
            ->where('slug', 'home')
            ->where('status', 'published')
            ->with(['elements.field'])
            ->first();

        return view('livewire.frontend.home', [
            'entry' => $entry,
        ]);
    }
}
