<?php

namespace App\Livewire\Frontend;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class ContactPage extends Component
{
    #[Layout('components.layouts.frontend')]
    #[Title('Contact')]
    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        $entry = \App\Models\Entry::query()->where('slug', 'contact')
            ->where('status', 'published')
            ->with(['elements.blueprintElement'])
            ->first();

        return view('livewire.frontend.contact-page', [
            'entry' => $entry,
        ]);
    }
}
