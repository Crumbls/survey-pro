<?php

namespace App\Policies;

use App\Models\Ability;
use App\Models\User;

class AbilityPolicy extends AbstractPolicy {

    public function viewAny(?User $user) : bool {
        if (!static::isRequestFilament()) {
            return false;
        }
        return true;
    }
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, $record): bool
    {
        return true;
        //
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
        //
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, $record): bool
    {
        return true;
        //
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, $record): bool
    {
        return true;
        //
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, $record): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, $record): bool
    {
        //
    }

    public static function getModelClass(): string
    {
        return Ability::class;
    }
}
