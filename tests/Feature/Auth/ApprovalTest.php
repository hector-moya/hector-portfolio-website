<?php

use App\Models\User;

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
