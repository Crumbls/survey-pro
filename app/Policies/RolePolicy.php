<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;
use App\Models\Role as A;
use Spatie\Permission\Models\Role as B;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy extends AbstractPolicy
{

    /**
     * Determine whether the user can view any$record models.
     */
    public function viewAny(User $user): bool
    {
        return true;
        return $user->can('view_any_role');
    }


    public static function getModelClass(): string
    {
        return Role::class;
        // TODO: Implement getModelClass() method.
    }
}
