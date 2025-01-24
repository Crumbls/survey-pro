<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class TenantUserRole extends Pivot
{
    protected $table = 'tenant_user_role';

    public function tenant() : BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function role() : BelongsTo
    {
        return $this->belongsTo(Role::class);
    }
}
