<?php

use App\Livewire\Users\Index;
use App\Mail\UserApproved;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;

beforeEach(function (): void {
    SiteSetting::set('registration_mode', 'open');
    $this->admin = User::factory()->admin()->create();
    $this->pendingUser = User::factory()->pending()->create();
});

test('admin can approve a pending user', function (): void {
    Mail::fake();

    Livewire::actingAs($this->admin)
        ->test(Index::class)
        ->call('approve', $this->pendingUser->id);

    expect($this->pendingUser->fresh()->status)->toBe('active');
});

test('approved user receives an email', function (): void {
    Mail::fake();

    Livewire::actingAs($this->admin)
        ->test(Index::class)
        ->call('approve', $this->pendingUser->id);

    Mail::assertQueued(UserApproved::class, fn ($mail) => $mail->hasTo($this->pendingUser->email));
});

test('non-admin cannot approve a user', function (): void {
    $editor = User::factory()->editor()->create();

    Livewire::actingAs($editor)
        ->test(Index::class)
        ->assertForbidden();
});

test('admin policy allows approve', function (): void {
    expect($this->admin->can('approve', $this->pendingUser))->toBeTrue();
});

test('editor policy denies approve', function (): void {
    $editor = User::factory()->editor()->create();
    expect($editor->can('approve', $this->pendingUser))->toBeFalse();
});

test('pending users show pending badge in users index', function (): void {
    Livewire::actingAs($this->admin)
        ->test(Index::class)
        ->assertSee('Pending');
});
