<?php

declare(strict_types=1);

use App\Livewire\Settings\Password;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

test('password can be updated', function () {
    $user = User::factory()->create([
        'password' => Hash::make('password'),
    ]);

    $this->actingAs($user);

    $response = Livewire::test(Password::class)
        ->set('form.current_password', 'password')
        ->set('form.password', 'new-password')
        ->set('form.password_confirmation', 'new-password')
        ->call('updatePassword');

    $response->assertHasNoErrors();

    expect(Hash::check('new-password', $user->refresh()->password))->toBeTrue();
});

test('correct password must be provided to update password', function () {
    $user = User::factory()->create([
        'password' => Hash::make('password'),
    ]);

    $this->actingAs($user);

    $response = Livewire::test(Password::class)
        ->set('form.current_password', 'wrong-password')
        ->set('form.password', 'new-password')
        ->set('form.password_confirmation', 'new-password')
        ->call('updatePassword');

    $response->assertHasErrors(['form.current_password']);
});
