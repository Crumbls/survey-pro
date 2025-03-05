<?php

namespace App\Policies;

use App\Models\Collector;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CollectorPolicy extends AbstractPolicy
{
    public function viewAny(?User $user) : bool {
        if (!static::isRequestFilament()) {
            return parent::viewAny($user);
            return false;
        }
        return true;
    }

    public static function getModelClass(): string
    {
        return Collector::class;
    }
}
