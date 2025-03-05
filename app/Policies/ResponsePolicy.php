<?php

namespace App\Policies;

use App\Models\Response;
use App\Models\User;

class ResponsePolicy extends AbstractPolicy
{
    public function viewAny(?User $user) : bool {
        if (!static::isRequestFilament()) {
            return parent::viewAny($user);
        }
        return true;
    }


    public static function getModelClass(): string
    {
        return Response::class;
    }
}
