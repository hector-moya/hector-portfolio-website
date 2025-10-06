<?php

namespace App\Livewire\Frontend;

use App\Models\Entry;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class ContactPage extends Component
{
    #[Layout('components.layouts.frontend')]
    #[Title('Contact')]
    public function render()
    {
        $entry = Entry::where('slug', 'contact')
            ->where('status', 'published')
            ->with(['elements.blueprintElement'])
            ->first();

        return view('livewire.frontend.contact-page', [
            'entry' => $entry,
        ]);
    }
}
