<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Silber\Bouncer\Database\Role;

class TenantUser extends Pivot
{
    protected $table = 'tenant_user';

    public function tenant() : BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
