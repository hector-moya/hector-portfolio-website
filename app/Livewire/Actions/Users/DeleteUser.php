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
        $user = User::findOrFail($userId);

        dump('Class', auth()->user()->id, $user->id);
        Gate::authorize('delete', [auth()->user(), $user]);

        return $user->delete();
    }
}
