<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Ability extends Model
{
//    use Concerns\IsAbility;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'title',
        'entity_id',
        'entity_type'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'int',
        'entity_id' => 'int',
        'only_owned' => 'boolean',
    ];

    /**
     * Constructor.
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }


    public function roles() : BelongsToMany {
        return $this->belongsToMany(Role::class, 'permissions')
            ->withPivot('forbidden')
            ->using(Permission::class);
    }
}
