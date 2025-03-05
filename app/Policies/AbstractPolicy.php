<?php

namespace App\Policies;

use App\Models\Ability;
use App\Models\User;
use App\Services\TenantService;
use Filament\Facades\Filament;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

abstract class AbstractPolicy {
    use HandlesAuthorization;

    abstract public static function getModelClass() : string;

    /**
     * Get the current tenant ID from the request, if available.
     */
    protected function getCurrentTenantId(): ?int
    {
        $request = request();
        return $request->tenant ? $request->tenant->getKey() : $request->header('X-Tenant-ID');
    }

    /**
     * Generic ability check that can be used by all policy methods.
     *
     * @param User $user The user to check permissions for
     * @param string $ability The ability name (e.g., 'view', 'create', 'update', 'delete')
     * @param mixed|null $model The model instance or null for class-level checks
     * @return bool
     */
    protected function checkAbility(User $user, string $ability, $model = null): bool
    {
        $tenantId = $this->getCurrentTenantId();
        $entityId = $model?->id;
        $entityType = static::getModelClass();

        /**
         * TODO: Remove this.
         */
        if (!$user->hasAbility($ability, $entityType, $entityId, $tenantId) && $role = $user->roles->where('id','<>',8)->first()) {
//            $this->autoAddPermission($user, $ability, $entityType);
        }

        return $user->hasAbility($ability, $entityType, $entityId, $tenantId);
    }


    /**
     * Automatically add a permission to the user's appropriate role.
     * Only for use in debug mode.
     *
     * @param User $user The user to add permission for
     * @param string $ability The ability name to add
     * @param string $entityType The entity type for this ability
     * @return bool True if permission was added, false otherwise
     */
    private function autoAddPermission(User $user, string $ability, string $entityType): bool
    {
        // First, find a suitable role to add the permission to
        // 1. Prefer a tenant role if a tenant ID is set
        // 2. Otherwise use a global role
        // 3. Exclude any specific roles you don't want to modify (e.g., admin roles)

        $tenantId = $this->getCurrentTenantId();
        $excludedRoleIds = [8]; // Role IDs that should never be auto-modified

        // Get the appropriate role
        $role = null;

        if ($tenantId) {
            // Get tenant-specific role
            $role = $user->roles()
                ->wherePivot('tenant_id', $tenantId)
                ->whereNotIn('roles.id', $excludedRoleIds)
                ->first();
        }

        // If no tenant role found or no tenant ID specified, try global role
        if (!$role) {
            $role = $user->roles()
                ->wherePivot('tenant_id', null)
                ->whereNotIn('roles.id', $excludedRoleIds)
                ->first();
        }

        // If no suitable role found, we can't add permission
        if (!$role) {
            Log::warning("Could not auto-add permission: no suitable role found", [
                'user_id' => $user->id,
                'ability' => $ability,
                'entity_type' => $entityType
            ]);
            return false;
        }

        // Find or create the ability
        $abilityRecord = Ability::firstOrCreate(
            ['name' => $ability, 'entity_type' => $entityType],
            [
                'title' => ucwords(str_replace('_', ' ', $ability)) . ' ' . class_basename($entityType),
                'entity_id' => null // This ability applies to all entities of this type
            ]
        );

        // Check if permission already exists
        $permissionExists = DB::table('permissions')
            ->where('role_id', $role->id)
            ->where('ability_id', $abilityRecord->id)
            ->exists();

        if ($permissionExists) {
            // Permission exists but might be forbidden, update it
            DB::table('permissions')
                ->where('role_id', $role->id)
                ->where('ability_id', $abilityRecord->id)
                ->update(['forbidden' => false]);
        } else {
            // Create new permission
            DB::table('permissions')->insert([
                'role_id' => $role->id,
                'ability_id' => $abilityRecord->id,
                'forbidden' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return true;
    }

    public static function isRequestFilament() : bool {
        return once(function() {
            $panel = Filament::getCurrentPanel();
            $path = $panel?->getPath();
            if (!$path) {
                return false;
            }
            return (request()->is($path) || request()->is($path.'/*') || request()->is('filament/*'));
        });
    }

    public static function canAccessFilament() : bool {
        return once(function() {
            if (!static::isRequestFilament()) {
                return false;
            }
            return auth()->user()->canAccessPanel(Filament::getCurrentPanel($panel));
        });
    }

    public static function getModelName() : string {
        return once(function() {
            return app()->getNamespace().'Models\\'.Str::of(class_basename(get_called_class()))->chopEnd('Policy');
        });
    }


    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $this->checkAbility($user, 'viewAny');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, mixed $record): bool
    {
        return $this->checkAbility($user, 'view', $record);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $this->checkAbility($user, 'create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, mixed $record): bool
    {
        return $this->checkAbility($user, 'update', $record);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, mixed $record): bool
    {
        return $this->checkAbility($user, 'delete', $record);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, mixed $record): bool
    {
        return $this->checkAbility($user, 'restore', $record);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, mixed $record): bool
    {
        return $this->checkAbility($user, 'forceDelete', $record);
    }
}
