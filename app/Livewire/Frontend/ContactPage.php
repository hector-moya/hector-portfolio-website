<?php

namespace App\Livewire\Frontend;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Entry;
use Illuminate\Contracts\View\View;
use Illuminate\Contracts\View\Factory;
use Livewire\Component;

class ContactPage extends Component
{
    #[Layout('components.layouts.frontend')]
    #[Title('Contact')]
    public function render(): View|Factory
    {
        $entry = Entry::query()
            ->where('slug', 'contact')
            ->where('status', 'published')
            ->with(['elements.field'])
            ->first();

        return view('livewire.frontend.contact-page', [
            'entry' => $entry,
        ]);
    }
}
