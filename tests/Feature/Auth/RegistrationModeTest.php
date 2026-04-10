<?php

declare(strict_types=1);

use App\Models\Invitation;
use App\Models\SiteSetting;

beforeEach(function (): void {
    SiteSetting::set('registration_mode', 'closed');
});

test('register page is blocked when mode is closed', function (): void {
    $this->get(route('register'))
        ->assertRedirect(route('login'));
});

test('register page shows closed flash message', function (): void {
    $this->get(route('register'))
        ->assertSessionHas('status', 'Registration is currently closed.');
});

test('register page is accessible when mode is open', function (): void {
    SiteSetting::set('registration_mode', 'open');

    $this->get(route('register'))
        ->assertOk();
});

test('register page is accessible when mode is approval', function (): void {
    SiteSetting::set('registration_mode', 'approval');

    $this->get(route('register'))
        ->assertOk();
});

test('register page is blocked when mode is invitation and no token provided', function (): void {
    SiteSetting::set('registration_mode', 'invitation');

    $this->get(route('register'))
        ->assertRedirect(route('login'));
});

test('register page is accessible with a valid invitation token', function (): void {
    SiteSetting::set('registration_mode', 'invitation');

    $invitation = Invitation::factory()->create();

    $this->get(route('register', ['token' => $invitation->token]))
        ->assertOk();
});

test('register page is blocked with an expired invitation token', function (): void {
    SiteSetting::set('registration_mode', 'invitation');

    $invitation = Invitation::factory()->expired()->create();

    $this->get(route('register', ['token' => $invitation->token]))
        ->assertRedirect(route('login'));
});

test('register page is blocked with an already-accepted invitation token', function (): void {
    SiteSetting::set('registration_mode', 'invitation');

    $invitation = Invitation::factory()->accepted()->create();

    $this->get(route('register', ['token' => $invitation->token]))
        ->assertRedirect(route('login'));
});
