<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Navigation;
use App\Models\User;

class NavigationPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Navigation $navigation): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Navigation $navigation): bool
    {
        return true;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Navigation $navigation): bool
    {
        return true;
    }

    /**
     * Determine whether the user can manage navigation items.
     */
    public function manageItems(User $user, Navigation $navigation): bool
    {
        return true;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Navigation $navigation): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Navigation $navigation): bool
    {
        return false;
    }
}
