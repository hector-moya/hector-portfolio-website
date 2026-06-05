<?php

declare(strict_types=1);

namespace App\Livewire\Frontend;

use App\Livewire\Forms\ContactForm as ContactFormData;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

final class ContactForm extends Component
{
    public ContactFormData $form;

    public string $title = '';

    /**
     * Honeypot field. Real users never see or fill this; bots usually do.
     */
    public string $website = '';

    public bool $sent = false;

    public function submit(): void
    {
        // Silently accept (without persisting) when the honeypot is filled,
        // so bots receive a success state and move on.
        if ($this->website !== '') {
            $this->reset('website');
            $this->sent = true;

            return;
        }

        $this->ensureIsNotRateLimited();

        $this->form->submit();

        RateLimiter::hit($this->throttleKey(), 3600);

        $this->sent = true;
    }

    public function render(): View|Factory
    {
        return view('livewire.frontend.contact-form');
    }

    private function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'form.message' => __('Too many submissions. Please try again in :minutes minutes.', [
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    private function throttleKey(): string
    {
        return 'contact-form|'.request()->ip();
    }
}
