<?php

namespace App\Policies;

use App\Models\Client;
use App\Models\User;
use App\Services\AuthorizationCache;
use Filament\Facades\Filament;

class ClientPolicy extends AbstractPolicy {
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Client $record): bool
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
    public function update(User $user, Client $record): bool
    {
        return true;
        //
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Client $record): bool
    {
        return true;
        //
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Client $record): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Client $record): bool
    {
        //
    }
}
