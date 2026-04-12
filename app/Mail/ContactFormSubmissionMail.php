<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\FormSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

final class ContactFormSubmissionMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function __construct(public FormSubmission $submission) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            replyTo: $this->submission->email,
            subject: 'New Contact Form Submission from '.$this->submission->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact-form-submission',
        );
    }
}
