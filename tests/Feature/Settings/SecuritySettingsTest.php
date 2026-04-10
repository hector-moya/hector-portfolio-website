<?php

declare(strict_types=1);

use App\Livewire\Settings\Security;
use App\Models\Invitation;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;

beforeEach(function (): void {
    SiteSetting::set('registration_mode', 'open');
});

test('admin can access security settings', function (): void {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('settings.security'))
        ->assertOk();
});

test('non-admin cannot access security settings', function (): void {
    $editor = User::factory()->editor()->create();

    $this->actingAs($editor)
        ->get(route('settings.security'))
        ->assertForbidden();
});

test('admin can change registration mode', function (): void {
    $admin = User::factory()->admin()->create();

    Livewire::actingAs($admin)
        ->test(Security::class)
        ->set('registrationMode', 'invitation')
        ->call('saveMode')
        ->assertHasNoErrors();

    expect(SiteSetting::get('registration_mode'))->toBe('invitation');
});

test('registration mode must be a valid value', function (): void {
    $admin = User::factory()->admin()->create();

    Livewire::actingAs($admin)
        ->test(Security::class)
        ->set('registrationMode', 'invalid-mode')
        ->call('saveMode')
        ->assertHasErrors(['registrationMode']);
});

test('admin can send an invitation from security settings', function (): void {
    Mail::fake();

    $admin = User::factory()->admin()->create();

    Livewire::actingAs($admin)
        ->test(Security::class)
        ->set('registrationMode', 'invitation')
        ->call('saveMode')
        ->set('inviteEmail', 'newperson@example.com')
        ->set('inviteRole', 'editor')
        ->call('sendInvite')
        ->assertHasNoErrors();

    expect(Invitation::where('email', 'newperson@example.com')->exists())->toBeTrue();
});

test('invite email must be unique in invitations table', function (): void {
    $admin = User::factory()->admin()->create();
    Invitation::factory()->create(['email' => 'existing@example.com']);

    Livewire::actingAs($admin)
        ->test(Security::class)
        ->set('inviteEmail', 'existing@example.com')
        ->set('inviteRole', 'viewer')
        ->call('sendInvite')
        ->assertHasErrors(['inviteEmail']);
});
