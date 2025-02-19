<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Role extends Model
{
//    use Concerns\IsRole;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [

        'name',
        'title',
        'tenant_id'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'int',
    ];

    public static function booted() : void {
        static::creating(function(Model $record) {
            if (empty($record->name)) {
                $uuid = $record->title ? Str::kebab($record->title) : (string)Str::uuid();
                while ($record::where('name', $uuid)->count()) {
                    $uuid = (string)Str::uuid();
                }
                $record->name = $uuid;
            }
        });
    }

    public function tenant() : BelongsTo {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * The users relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphedToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'tenant_user')
            ->using(TenantUserRole::class);
    }

    public function abilities() : BelongsToMany {
            return $this->belongsToMany(Ability::class, 'permissions')
                ->withPivot('forbidden')
                ->using(Permission::class)
                ;
    }
}
