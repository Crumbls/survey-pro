<?php

namespace App\Policies;

use App\Models\Client;
use App\Models\Report;
use App\Models\Survey;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Str;

class ReportPolicy extends AbstractPolicy
{

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Report $report): bool
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
        return rand(0,1);
        return true;
        //
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Report $record): bool
    {
        return once(function() use ($user, $record) {
            /**
             * Determine if they match the client first.
             */
            if (!$record->client_id) {
                return false;
            }
            if (!$record->client->tenant->users()->where('users.id', $user->getKey())->take(1)->exists()) {
                return false;
            }

return true;
            $ret = Survey::whereIn('client_id',
                Client::whereIn('tenant_id',
                    $user->tenants()->select('tenants.id')
                )->select('clients.id')
            )->get();
            dd($ret, $record);
            dd($record);
            dd(__LINE__);
            return Survey::whereIn('surveys.tenant_id', $user->tenants()->select('tenants.id'))
                ->where('surveys.id', $record->getKey())
                ->take(1)
                ->count();
        });
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Report $report): bool
    {
        return false;
        //
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Report $report): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Report $report): bool
    {
        //
    }
}
