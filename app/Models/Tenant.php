<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tenant extends Model
{
    use HasFactory,
        HasUuid,
        SoftDeletes;

    protected $uuidFrom = 'name';

    protected $fillable = ['name'];

    public function users() : BelongsToMany
    {
        return $this->belongsToMany(User::class, 'tenant_user_role')
            ->withPivot('role_id')
            ->using(TenantUserRole::class);
    }

    public function surveys() : HasMany
    {
        return $this->hasMany(Survey::class);
    }

}
