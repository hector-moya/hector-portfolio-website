<?php

namespace App\Livewire\Actions\Users;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;

class CreateUser
{
    /**
     * Execute the action
     */
    public function create(array $userData): User
    {
        Gate::authorize('create', User::class);

        return User::query()->create([
            'name' => $userData['name'],
            'email' => $userData['email'],
            'password' => Hash::make($userData['password']),
            'role' => $userData['role'],
        ]);
    }
}
