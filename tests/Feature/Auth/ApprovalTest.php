<?php

use App\Livewire\Auth\Register;
use App\Mail\AdminPendingUserNotification;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;

test('pending user is redirected to pending-approval on any authenticated route', function (): void {
    $pending = User::factory()->pending()->create();

    $this->actingAs($pending)
        ->get(route('dashboard'))
        ->assertRedirect(route('pending-approval'));
});

test('active user is not redirected to pending-approval', function (): void {
    $active = User::factory()->admin()->create();

    $this->actingAs($active)
        ->get(route('dashboard'))
        ->assertOk();
});

test('pending user can access the pending-approval page', function (): void {
    $pending = User::factory()->pending()->create();

    $this->actingAs($pending)
        ->get(route('pending-approval'))
        ->assertOk();
});

test('unauthenticated user cannot access pending-approval page', function (): void {
    $this->get(route('pending-approval'))
        ->assertRedirect(route('login'));
});

test('admin receives email when user registers in approval mode', function (): void {
    Mail::fake();
    SiteSetting::set('registration_mode', 'approval');

    $admin = User::factory()->admin()->create();

    Livewire::test(Register::class)
        ->set('name', 'New Person')
        ->set('email', 'newperson@example.com')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->call('register');

    Mail::assertQueued(AdminPendingUserNotification::class, fn ($mail) => $mail->hasTo($admin->email));
});
