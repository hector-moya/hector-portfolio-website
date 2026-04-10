<?php

declare(strict_types=1);

namespace App\Livewire\Actions\Settings;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

final class UpdatePassword
{
    public function update(User $user, string $password): void
    {
        $user->update([
            'password' => Hash::make($password),
        ]);
    }
}
