<?php

namespace App\Policies;

use Crumbls\Issue\Models\Issue;
use App\Models\User;

class IssuePolicy extends AbstractPolicy
{
    public function viewAny(?User $user) : bool {
        if (!static::isRequestFilament()) {
            return false;
        }
        return true;
    }

    public static function getModelClass(): string
    {
        return Issue::class;
    }
}
