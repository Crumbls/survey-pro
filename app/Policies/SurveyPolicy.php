<?php

namespace App\Policies;

use App\Models\Survey;
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

    public function viewAny(?User $user) : bool {
        return true;
        if (!static::isRequestFilament()) {
            return false;
        }
        return true;
    }
    /**
     * Determine whether the user can view the model.
     */
    public function view(?User $user, $record): bool
    {
        return true;
        //
    }

    public static function getModelClass(): string
    {
        return Survey::class;
        // TODO: Implement getModelClass() method.
    }
}
