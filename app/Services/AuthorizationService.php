<?php

namespace App\Services;

use App\Repositories\AdminUserRepository;

/**
 * Centralized role and permission checks for admin users.
 */
class AuthorizationService extends BaseService
{
    public function __construct(private AdminUserRepository $users)
    {
    }

    public function hasRole(int $adminUserId, string $role): bool
    {
        if ($this->isSuperAdmin($adminUserId)) {
            return true;
        }

        return in_array($role, $this->users->roleSlugs($adminUserId), true);
    }

    public function hasPermission(int $adminUserId, string $permission): bool
    {
        if ($this->isSuperAdmin($adminUserId)) {
            return true;
        }

        return in_array($permission, $this->users->permissionSlugs($adminUserId), true);
    }

    public function isSuperAdmin(int $adminUserId): bool
    {
        return in_array('super-admin', $this->users->roleSlugs($adminUserId), true);
    }
}
