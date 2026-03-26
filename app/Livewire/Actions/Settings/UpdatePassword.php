<?php

namespace App\Livewire\Actions\Settings;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UpdatePassword
{
    public function update(User $user, string $password): void
    {
        $user->update([
            'password' => Hash::make($password),
        ]);
    }
}
