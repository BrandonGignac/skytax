<?php

namespace Vanguard\Support\Authorization;

use Vanguard\Role;

trait AuthorizationUserTrait
{
    /**
     * @return mixed
     */
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    /**
     * Check if user has specified role.
     * @param $role
     * @return bool
     */
    public function hasRole($role)
    {
        return $this->role->name === $role;
    }

    /**
     * Check if user can perform some action.
     * @param $permission
     * @return bool
     */
    public function hasPermission($permission)
    {
        $permission = (array) $permission;
        $permissions = $this->role->cachedPermissions()->pluck('name')->toArray();

        foreach ($permission as $perm) {
            if (! in_array($perm, $permissions, true)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Set user's role.
     * @param Role $role
     * @return mixed
     */
    public function setRole($role)
    {
        return $this->forceFill([
            'role_id' => $role instanceof Role ? $role->id : $role
        ])->save();
    }
}
