<?php

namespace App\Livewire\Actions\Users;

use App\Models\User;

class DeleteUser
{
    /**
     * Execute the action
     */
    public function execute(User $user): bool
    {
        return $user->delete();
    }
}
