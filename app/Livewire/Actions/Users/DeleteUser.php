<?php

namespace App\Livewire\Actions\Users;

use App\Models\User;
use Illuminate\Support\Facades\Gate;

class DeleteUser
{
    /**
     * Execute the action
     */
    public function delete(int $userId): bool
    {
        $user = User::query()->findOrFail($userId);

        Gate::authorize('delete', $user);

        return $user->delete();
    }
}
