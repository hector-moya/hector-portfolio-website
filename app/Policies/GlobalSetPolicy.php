<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;

final class GlobalSetPolicy
{
    /**
     * Determine whether the user can view any global sets.
     */
    public function viewAny(): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the global set.
     */
    public function view(): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create global sets.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the global set.
     */
    public function update(): bool
    {
        return true;
    }

    /**
     * Determine whether the user can delete the global set.
     */
    public function delete(): bool
    {
        return true;
    }
}
