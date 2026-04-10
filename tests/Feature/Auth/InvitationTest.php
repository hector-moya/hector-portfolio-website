<?php

declare(strict_types=1);

use App\Livewire\Actions\Users\InviteUser;
use App\Livewire\Auth\Register;
use App\Mail\InvitationMail;
use App\Models\Invitation;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;

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
    Mail::assertQueued(InvitationMail::class, fn ($mail) => $mail->hasTo('invited@example.com'));
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

    Mail::assertQueued(InvitationMail::class, function (InvitationMail $mail) {
        $invitation = Invitation::where('email', 'invited@example.com')->first();

        return $mail->invitation->token === $invitation->token;
    });
});

test('register component pre-fills email from valid invitation token', function (): void {
    SiteSetting::set('registration_mode', 'invitation');

    $invitation = Invitation::factory()->create(['email' => 'invited@example.com']);

    Livewire::test(Register::class, ['token' => $invitation->token])
        ->assertSet('email', 'invited@example.com');
});

test('registering with invitation token marks invitation as accepted', function (): void {
    Mail::fake();
    SiteSetting::set('registration_mode', 'invitation');

    $invitation = Invitation::factory()->create(['email' => 'invited@example.com']);

    Livewire::test(Register::class, ['token' => $invitation->token])
        ->set('name', 'Invited User')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->call('register')
        ->assertHasNoErrors();

    expect($invitation->fresh()->accepted_at)->not->toBeNull();
});

test('registering in approval mode sets user status to pending', function (): void {
    Mail::fake();
    SiteSetting::set('registration_mode', 'approval');

    Livewire::test(Register::class)
        ->set('name', 'Pending User')
        ->set('email', 'pending@example.com')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->call('register')
        ->assertHasNoErrors();

    $user = User::where('email', 'pending@example.com')->first();
    expect($user->status)->toBe('pending');
});

test('registering with mismatched email and invitation token fails', function (): void {
    SiteSetting::set('registration_mode', 'invitation');

    $invitation = Invitation::factory()->create(['email' => 'invited@example.com']);

    Livewire::test(Register::class, ['token' => $invitation->token])
        ->set('email', 'other@example.com')
        ->set('name', 'Attacker')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->call('register')
        ->assertHasErrors(['email']);
});
