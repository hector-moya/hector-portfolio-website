<?php

namespace App\Livewire\Frontend;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class Home extends Component
{
    #[Layout('components.layouts.frontend')]
    #[Title('Home')]
    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        $entry = \App\Models\Entry::query()->where('slug', 'home')
            ->where('status', 'published')
            ->with(['elements.blueprintElement'])
            ->first();

        return view('livewire.frontend.home', [
            'entry' => $entry,
        ]);
    }
}
