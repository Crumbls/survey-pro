<?php

namespace App\Models;

use App\Traits\HasSubscriptions;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Livewire\Features\SupportFileUploads\WithFileUploads;
use Masterix21\Addressable\Models\Concerns\HasAddresses;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class RoleTemplate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'is_global',
        'default_abilities',
        'is_active'
    ];

    protected $casts = [
        'default_abilities' => 'array',
        'is_global' => 'boolean',
        'is_active' => 'boolean'
    ];

    /**
     * Get the roles that were created from this template
     */
    public function roles()
    {
        return $this->hasMany(Role::class, 'template_id');
    }

    /**
     * Scope a query to only include active templates
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include global templates
     */
    public function scopeGlobal($query)
    {
        return $query->where('is_global', true);
    }

    /**
     * Scope a query to only include tenant-specific templates
     */
    public function scopeTenantSpecific($query)
    {
        return $query->where('is_global', false);
    }
}
