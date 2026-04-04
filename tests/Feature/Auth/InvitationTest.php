<?php

use App\Livewire\Actions\Users\InviteUser;
use App\Mail\InvitationMail;
use App\Models\Invitation;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Mail;

beforeEach(function (): void {
    SiteSetting::set('registration_mode', 'open');
});

test('admin can send an invitation', function (): void {
    Mail::fake();

    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    app(InviteUser::class)->invite(
        email: 'invited@example.com',
        role: 'editor',
    );

    expect(Invitation::where('email', 'invited@example.com')->exists())->toBeTrue();
    Mail::assertSent(InvitationMail::class, fn ($mail) => $mail->hasTo('invited@example.com'));
});

test('invitation expires in 48 hours', function (): void {
    Mail::fake();

    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    app(InviteUser::class)->invite(
        email: 'invited@example.com',
        role: 'viewer',
    );

    $invitation = Invitation::where('email', 'invited@example.com')->first();

    expect($invitation->expires_at)->toBeBetween(
        now()->addHours(47),
        now()->addHours(49)
    );
});

test('non-admin cannot send an invitation', function (): void {
    $editor = User::factory()->editor()->create();
    $this->actingAs($editor);

    expect(fn () => app(InviteUser::class)->invite(
        email: 'invited@example.com',
        role: 'viewer',
    ))->toThrow(AuthorizationException::class);
});

test('invitation email contains registration link with token', function (): void {
    Mail::fake();

    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    app(InviteUser::class)->invite(
        email: 'invited@example.com',
        role: 'viewer',
    );

    Mail::assertSent(InvitationMail::class, function (InvitationMail $mail) {
        $invitation = Invitation::where('email', 'invited@example.com')->first();

        return $mail->invitation->token === $invitation->token;
    });
});
