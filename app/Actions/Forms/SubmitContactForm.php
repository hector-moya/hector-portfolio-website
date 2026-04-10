<?php

declare(strict_types=1);

namespace App\Actions\Forms;

use App\Mail\ContactFormSubmissionMail;
use App\Models\FormSubmission;
use Illuminate\Support\Facades\Mail;

final class SubmitContactForm
{
    public function handle(array $data): FormSubmission
    {
        $submission = FormSubmission::query()->create([
            'form' => 'contact',
            'name' => $data['name'],
            'email' => $data['email'],
            'message' => $data['message'],
        ]);

        $adminEmail = config('mail.from.address');
        if ($adminEmail) {
            Mail::to($adminEmail)->queue(new ContactFormSubmissionMail($submission));
        }

        return $submission;
    }
}
