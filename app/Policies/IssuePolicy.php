<?php

namespace App\Policies;

use Crumbls\Issue\Models\Issue;
use App\Models\User;

class IssuePolicy extends AbstractPolicy
{
    public function create(User $user) {
        return true;
    }
}
