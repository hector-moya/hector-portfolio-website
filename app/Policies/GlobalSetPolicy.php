<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\GlobalSet;
use App\Models\User;

class GlobalSetPolicy
{
    /**
     * Determine whether the user can view any global sets.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the global set.
     */
    public function view(User $user, GlobalSet $globalSet): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create global sets.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the global set.
     */
    public function update(User $user, GlobalSet $globalSet): bool
    {
        return true;
    }

    /**
     * Determine whether the user can delete the global set.
     */
    public function delete(User $user, GlobalSet $globalSet): bool
    {
        return true;
    }
}
