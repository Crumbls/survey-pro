<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use App\Services\AuthorizationCache;
use Filament\Facades\Filament;

class TenantPolicy extends AbstractPolicy {

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Tenant $record): bool
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
    public function update(User $user, Tenant $record): bool
    {
        return true;
        //
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Tenant $record): bool
    {
        return true;
        //
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Tenant $record): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Tenant $record): bool
    {
        //
    }
}
