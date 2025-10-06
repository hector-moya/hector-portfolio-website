<?php

namespace App\Policies;

use App\Models\Blueprint;
use App\Models\User;

class BlueprintPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can view blueprints
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Blueprint $blueprint): bool
    {
        return true; // All authenticated users can view individual blueprints
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->canEdit(); // Admins and editors can create
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Blueprint $blueprint): bool
    {
        return $user->canEdit(); // Admins and editors can update
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Blueprint $blueprint): bool
    {
        return $user->isAdmin(); // Only admins can delete
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Blueprint $blueprint): bool
    {
        return $user->isAdmin(); // Only admins can restore
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Blueprint $blueprint): bool
    {
        return $user->isAdmin(); // Only admins can force delete
    }
}
