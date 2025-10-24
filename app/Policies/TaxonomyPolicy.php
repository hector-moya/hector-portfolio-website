<?php

namespace App\Policies;

use App\Models\Taxonomy;
use App\Models\User;

class TaxonomyPolicy
{
    /**
     * Determine whether the user can view any taxonomies.
     */
    public function viewAny(User $user): bool
    {
        return $user->canEdit();
    }

    /**
     * Determine whether the user can view the taxonomy.
     */
    public function view(User $user, Taxonomy $taxonomy): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create taxonomies.
     */
    public function create(User $user): bool
    {
        return $user->canEdit();
    }

    /**
     * Determine whether the user can update the taxonomy.
     */
    public function update(User $user, Taxonomy $taxonomy): bool
    {
        return $user->canEdit();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the taxonomy.
     */
    public function restore(User $user, Taxonomy $taxonomy): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the taxonomy.
     */
    public function forceDelete(User $user, Taxonomy $taxonomy): bool
    {
        return $user->isAdmin();
    }
}
