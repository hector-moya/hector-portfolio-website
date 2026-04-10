<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Folder;
use App\Models\User;

final class FolderPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can view assets
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Folder $folder): bool
    {
        return true; // All authenticated users can view any folder
    }

    /**
     * Determine whether the user can create a folder.
     */
    public function create(User $user): bool
    {
        return $user->canEdit();
    }

    /**
     * Determine whether the user can update the folder.
     */
    public function update(User $user, Folder $folder): bool
    {
        return $user->role === 'admin' || $folder->created_by === $user->id;
    }

    /**
     * Determine whether the user can delete the folder.
     */
    public function delete(User $user, Folder $folder): bool
    {
        return $user->role === 'admin' || $folder->created_by === $user->id;
    }

    /**
     * Determine whether the user can restore the folder.
     */
    public function restore(User $user, Folder $folder): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the folder.
     */
    public function forceDelete(User $user, Folder $folder): bool
    {
        return false;
    }
}
