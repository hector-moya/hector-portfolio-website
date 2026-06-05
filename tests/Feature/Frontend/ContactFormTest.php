<?php

declare(strict_types=1);

use App\Livewire\Frontend\ContactForm;
use App\Mail\ContactFormSubmissionMail;
use App\Models\FormSubmission;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;

beforeEach(function (): void {
    RateLimiter::clear('contact-form|127.0.0.1');
});

test('a valid submission is stored and the admin is notified', function (): void {
    Mail::fake();

    Livewire::test(ContactForm::class)
        ->set('form.name', 'Ada Lovelace')
        ->set('form.email', 'ada@example.com')
        ->set('form.message', 'I love your portfolio.')
        ->call('submit')
        ->assertHasNoErrors()
        ->assertSet('sent', true);

    expect(FormSubmission::query()->where('email', 'ada@example.com')->exists())->toBeTrue();

    Mail::assertQueued(ContactFormSubmissionMail::class);
});

test('submission requires name, email and message', function (): void {
    Livewire::test(ContactForm::class)
        ->call('submit')
        ->assertHasErrors(['form.name', 'form.email', 'form.message'])
        ->assertSet('sent', false);

    expect(FormSubmission::query()->count())->toBe(0);
});

test('a filled honeypot is silently dropped without persisting', function (): void {
    Mail::fake();

    Livewire::test(ContactForm::class)
        ->set('form.name', 'Spam Bot')
        ->set('form.email', 'bot@example.com')
        ->set('form.message', 'buy stuff')
        ->set('website', 'http://spam.example')
        ->call('submit')
        ->assertSet('sent', true);

    expect(FormSubmission::query()->count())->toBe(0);
    Mail::assertNothingQueued();
});

test('submissions are rate limited per ip', function (): void {
    Mail::fake();

    RateLimiter::clear('contact-form|127.0.0.1');

    foreach (range(1, 5) as $i) {
        Livewire::test(ContactForm::class)
            ->set('form.name', "Sender {$i}")
            ->set('form.email', "sender{$i}@example.com")
            ->set('form.message', 'Hello there.')
            ->call('submit')
            ->assertHasNoErrors();
    }

    Livewire::test(ContactForm::class)
        ->set('form.name', 'Sender 6')
        ->set('form.email', 'sender6@example.com')
        ->set('form.message', 'Hello again.')
        ->call('submit')
        ->assertHasErrors('form.message');
});
