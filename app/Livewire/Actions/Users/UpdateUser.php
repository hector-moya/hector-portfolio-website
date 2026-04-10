<?php

declare(strict_types=1);

namespace App\Livewire\Actions\Users;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;

final class UpdateUser
{
    public function update(array $userData): User
    {

        $user = User::query()->findOrFail($userData['id']);
        Gate::authorize('update', $user);

        $updateData = [
            'name' => $userData['name'],
            'email' => $userData['email'],
            'role' => $userData['role'],
        ];

        // Only update password if provided
        if (! empty($userData['password'])) {
            $updateData['password'] = Hash::make($userData['password']);
        }

        $user->update($updateData);

        return $user;
    }
}
