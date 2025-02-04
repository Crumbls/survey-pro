<?php

namespace App\Policies;

use App\Models\Survey as Model;
use App\Models\User;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

/**
 * @extends AbstractPolicy<Survey>
 */
class SurveyPolicy extends AbstractPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     */
    public function view(?User $user, Model $record): bool
    {
        return true;
        //
    }

}
