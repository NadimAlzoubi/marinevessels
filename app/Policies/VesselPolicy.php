<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vessel;
use Illuminate\Auth\Access\Response;

class VesselPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Vessel $vessel): bool
    {
        return in_array($user->role, ['admin', 'editor', 'contributor', 'guest']);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'editor', 'contributor', 'guest']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Vessel $vessel): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Vessel $vessel): bool
    {
        return in_array($user->role, ['admin', 'editor', 'contributor', 'guest']);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Vessel $vessel): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Vessel $vessel): bool
    {
        return false;
    }
}
