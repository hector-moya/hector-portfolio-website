<?php

namespace App\Livewire\Forms;

use App\Actions\Forms\SubmitContactForm;
use App\Models\FormSubmission;
use Livewire\Attributes\Validate;
use Livewire\Form;

class ContactForm extends Form
{
    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|email|max:255')]
    public string $email = '';

    #[Validate('required|string|max:5000')]
    public string $message = '';

    public function submit(): FormSubmission
    {
        $this->validate();

        $submission = app(SubmitContactForm::class)->handle([
            'name' => $this->name,
            'email' => $this->email,
            'message' => $this->message,
        ]);

        $this->reset('name', 'email', 'message');

        return $submission;
    }
}
