<?php

declare(strict_types=1);

namespace App\Livewire\Actions\Settings;

use App\Models\User;

final class UpdateProfile
{
    public function update(User $user, array $data): User
    {
        $user->fill($data);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return $user;
    }
}
