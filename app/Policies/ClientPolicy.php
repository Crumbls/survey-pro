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
    public function viewAny(?User $user) : bool {
        if (!static::isRequestFilament()) {
            return parent::viewAny($user);
        }
        return true;
    }

    public static function getModelClass(): string
    {
        return Client::class;
        // TODO: Implement getModelClass() method.
    }
}
