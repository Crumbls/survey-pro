<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use App\Models\Role;

class TenantUserRole extends Pivot
{
    // Tell Laravel this model doesn't use an auto-incrementing ID
    public $incrementing = false;

    // Specify the primary key is composite
    protected $primaryKey = null;

    // No timestamps needed for pivot
    public $timestamps = false;


    protected $table = 'tenant_user_role';

    public $fillable = [
        'role_id',
        'tenant_id',
        'user_id'
    ];

    public function tenant() : BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @deprecated
     * @return BelongsTo
     */
    public function role() : BelongsTo
    {
        return $this->belongsTo(Role::class);
    }


    /**
     * Check if this is a global role (not tenant-specific).
     */
    public function isGlobal(): bool
    {
        return is_null($this->tenant_id);
    }
}
