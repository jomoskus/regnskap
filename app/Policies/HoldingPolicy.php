<?php

namespace App\Policies;

use App\Models\Holding;
use App\Models\User;

class HoldingPolicy
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
    public function view(User $user, Holding $holding): bool
    {
        return $user->id === $holding->user_id;
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
    public function update(User $user, Holding $holding): bool
    {
        return $user->id === $holding->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Holding $holding): bool
    {
        return $user->id === $holding->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Holding $holding): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Holding $holding): bool
    {
        return false;
    }
}
