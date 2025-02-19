<?php

namespace App\Policies;

use App\Models\Ability;
use App\Models\Client;
use App\Models\Permission;
use App\Models\User;
use App\Services\AuthorizationCache;
use Filament\Facades\Filament;
use App\Models\Role;

class ClientPolicy extends AbstractPolicy {
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Client $record): bool
    {
        return true;
        //
    }

    public function dis_viewAny(User $user): bool {

        return once(function() use ($user) {
            return \DB::table('abilities')
                ->whereIn('id', \DB::table('permissions')
                    ->where('permissions.entity_type','roles')
                    ->whereIn('permissions.entity_id', $user->roles()->select('roles.id'))
                    ->select('ability_id')
                )
                ->where(function(\Illuminate\Database\Query\Builder $sub) {
                    $sub->whereNull('abilities.scope');
                    if ($tenant = request()->tenant) {
                        $sub->orWhere('abilities.scope', $tenant->getKey());
                    }
                })
                ->whereNull('abilities.scope')
                ->where('abilities.entity_type', Client::class)
                //           ->where('abilities.name', 'viewAny')
                ->exists();
        });

dd(__LINE__);
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
