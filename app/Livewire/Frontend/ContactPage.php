<?php

namespace App\Livewire\Frontend;

use App\Livewire\Forms\ContactForm;
use App\Models\Entry;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class ContactPage extends Component
{
    public ContactForm $form;

    public bool $submitted = false;

    public function submit(): void
    {
        $this->form->submit();

        $this->submitted = true;
    }

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
