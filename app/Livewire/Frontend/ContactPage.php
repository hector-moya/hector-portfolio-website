<?php

namespace App\Livewire\Frontend;

use App\Mail\ContactFormSubmissionMail;
use App\Models\Entry;
use App\Models\FormSubmission;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;

// TODO: Extract the contact fields and validation into a ContactForm (app/Livewire/Forms/ContactForm.php,
//       extends Livewire\Form) using #[Validate] attributes for name, email, and message.
//       Add a submit() method to ContactForm that calls a SubmitContactForm action
//       (app/Actions/Forms/SubmitContactForm.php). The action should handle DB creation of the FormSubmission
//       record and queue the ContactFormSubmissionMail — keeping both persistence and side-effects out of the
//       component. The component's submit() should then just call $this->form->submit() and set $submitted = true.
//       Use app/Actions/ (not app/Livewire/Actions/) since this action is reusable outside Livewire (e.g. API).
class ContactPage extends Component
{
    #[Rule('required|string|max:255')]
    public string $name = '';

    #[Rule('required|email|max:255')]
    public string $email = '';

    #[Rule('required|string|max:5000')]
    public string $message = '';

    public bool $submitted = false;

    public function submit(): void
    {
        $this->validate();

        $submission = FormSubmission::query()->create([
            'form' => 'contact',
            'name' => $this->name,
            'email' => $this->email,
            'message' => $this->message,
        ]);

        $adminEmail = config('mail.from.address');
        if ($adminEmail) {
            Mail::to($adminEmail)->queue(new ContactFormSubmissionMail($submission));
        }

        $this->submitted = true;
        $this->reset('name', 'email', 'message');
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
