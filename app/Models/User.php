<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Services\ClientService;
use App\Services\TenantService;
use App\Traits\HasSubscriptions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

use Laravolt\Avatar\Avatar;

use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class User extends Authenticatable implements FilamentUser, HasMedia
{
    use HasApiTokens,
        HasFactory,
        InteractsWithMedia,
        Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public static function booted() : void {
        static::created(function(Model $record) {
        });

        static::deleting(function(Model $record) {
            TenantUserRole::where('user_id', $record->getKey())
                ->get()
                ->each(function(Model $record) {
                    $record->delete();
                });
        });
    }

    /**
     * TODO: Change this to a permission.
     * @param Panel $panel
     * @return bool
     */
    public function canAccessPanel(Panel $panel): bool {
        return Str::endsWith($this->email, ['@crumbls.com', '@o2group.com']);
    }


    public function tenants() : BelongsToMany
    {
        return $this->belongsToMany(Tenant::class, 'tenant_user_role')
            ->withPivot('role_id')
            ->using(TenantUserRole::class);
    }

    // a

    /**
     * Get all roles for this user across all tenants.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'tenant_user_role', 'user_id', 'role_id')
            ->withPivot('tenant_id');
//            ->using(UserRole::class);
        return $this->belongsToMany(Role::class, 'tenant_user_role', 'user_id', 'role_id')
            ->withPivot('tenant_id')
            ->using(TenantUserRole::class);
    }

    // b

    public function surveys() : HasMany
    {
        return $this->hasMany(Survey::class);
    }

    /**
     * Get the user's avatar URL with efficient retrieval and generation.
     *
     * @param int $size Size of the avatar (default 250)
     * @return string Avatar URL
     */
    public function getAvatar(int $size = 250): string
    {
        // Check if avatar already exists using eager loading of media
        $avatarMedia = $this->getFirstMedia('avatar');

        if ($avatarMedia) {
            return $avatarMedia->getUrl();
        }

        // Generate avatar if not exists
        return $this->generateAndStoreAvatar($size);
    }

    /**
     * Generate and store avatar for the user.
     *
     * @param int $size Size of the avatar
     * @return string Avatar URL
     */
    public function generateAndStoreAvatar(int $size = 250): string
    {
        // Generate avatar using Laravolt
        $avatarImage = new Avatar();

        $avatarImage->create($this->name)
            ->setDimension($size)
            ->setFontSize($size / 2)
            ->toBase64();

        // Convert base64 to image file
        $avatarContent = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $avatarImage));

        // Generate a unique filename
        $filename = 'avatar_' . $this->id . '_' . now()->timestamp . '.png';

        // Store the avatar using Media Library
        $this->addMediaFromString($avatarContent)
            ->usingName($filename)
            ->usingFileName($filename)
            ->toMediaCollection('avatar');

        // Retrieve and return the URL of the stored avatar
        return $this->getFirstMediaUrl('avatar');
    }

    /**
     * Clear existing avatar and regenerate.
     *
     * @param int $size Size of the avatar
     * @return string New avatar URL
     */
    public function refreshAvatar(int $size = 250): string
    {
        // Clear existing avatar media
        $this->clearMediaCollection('avatar');

        // Generate and store new avatar
        return $this->generateAndStoreAvatar($size);
    }

    /**
     * Scope to eager load avatar media.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithAvatar($query)
    {
        return $query->with('media');
    }


    /**
     * @deprecated
     * @return mixed
     */
    public function currentTenantRole()
    {
        return $this->tenantRoles()
            ->where('tenant_id', session('current_tenant_id'))
            ->first();
    }


    /**
     * Get only the global roles for this user (where tenant_id is null).
     */
    public function globalRoles()
    {
        return $this->roles()
            ->wherePivot('tenant_id', null);
    }

    /**
     * Get the role for a specific tenant.
     */
    public function getRoleForTenant(int $tenantId)
    {
        return $this->roles()
            ->wherePivot('tenant_id', $tenantId)
            ->first();
    }

    /**
     * Get all abilities for this user, cached for performance.
     *
     * @param int|null $tenantId If provided, get only abilities for this tenant
     * @return Collection
     */
    public function getAbilities(?int $tenantId = null): Collection
    {
        return once(function () use ($tenantId) {
            // Get relevant roles based on tenant_id
            $roles = $tenantId
                ? $this->roles()->wherePivot('tenant_id', $tenantId)->get()
                : $this->roles()->wherePivot('tenant_id', null)->get();

            if ($roles->isEmpty()) {
                return collect();
            }

            // Get role IDs
            $roleIds = $roles->pluck('id')->toArray();

            // Get abilities through permissions table, filtering out forbidden ones
            return DB::table('abilities')
                ->join('permissions', 'abilities.id', '=', 'permissions.ability_id')
                ->whereIn('permissions.role_id', $roleIds)
                ->where('permissions.forbidden', false)
                ->select('abilities.*')
                ->get();
        });

    }

    /**
     * Get global abilities (not associated with any tenant).
     *
     * @return Collection
     */
    public function getGlobalAbilities(): Collection
    {
        return $this->getAbilities();
    }

    /**
     * Get all abilities across all tenants and global roles.
     *
     * @return Collection
     */
    public function getAllAbilities(): Collection
    {
        $cacheKey = "user_{$this->id}_all_abilities";

        Cache::forget($cacheKey);

        return Cache::remember($cacheKey, now()->addHours(24), function () {
            // Get all roles for this user
            $roles = $this->roles()->get();

            if ($roles->isEmpty()) {
                return collect();
            }

            // Get role IDs
            $roleIds = $roles->pluck('id')->toArray();

            // Get abilities through permissions table, filtering out forbidden ones
            return DB::table('abilities')
                ->join('permissions', 'abilities.id', '=', 'permissions.ability_id')
                ->whereIn('permissions.role_id', $roleIds)
                ->where('permissions.forbidden', false)
                ->select('abilities.*')
                ->get();
        });
    }

    /**
     * Clear the abilities cache for this user.
     *
     * @return void
     */
    public function clearAbilitiesCache(): void
    {
        // Clear global abilities cache
        Cache::forget("user_{$this->id}_abilities_global");

        // Clear all abilities cache
        Cache::forget("user_{$this->id}_all_abilities");

        // Clear tenant-specific abilities cache
        $tenantIds = $this->tenants()->pluck('tenants.id');
        foreach ($tenantIds as $tenantId) {
            Cache::forget("user_{$this->id}_abilities_tenant_{$tenantId}");
        }
    }

    /**
     * Check if the user has a specific ability.
     *
     * @param string $ability The ability name to check
     * @param string $entityType The entity type to check against
     * @param int|null $entityId The specific entity ID (optional)
     * @param int|null $tenantId The tenant context (optional)
     * @return bool
     */
    public function hasAbility(string $ability, string $entityType, ?int $entityId = null, ?int $tenantId = null): bool
    {

        $abilities = $tenantId && false
            ? $this->getAbilities($tenantId)
            : $this->getAllAbilities();

//        dd($abilities->where('name',$ability), $ability);

        $query = $abilities->where('name', $ability)
            ->where('entity_type', $entityType);
//dd($query);
        if ($entityId) {
//            dd($query, $collection);
            // Match specific entity ID or null (applies to all)
            return $query->where(function (\stdClass $ability) use ($entityId) {
                if ($ability->entity_id === null) {
                    return true;
                } else if ($ability->entity_id == $entityId) {
                    return true;
                }
                return false;
            })->isNotEmpty();
        }

        return $query->isNotEmpty();
    }
}
