<?php

namespace App\Middleware;

use App\Exceptions\AuthorizationException;
use App\Repositories\AdminUserRepository;
use App\Services\AuthorizationService;
use App\Support\DatabaseFactory;

/**
 * RequireAdmin - Admin-only middleware
 *
 * Ensures user has admin role before accessing protected routes.
 */
class RequireAdmin extends Middleware
{
    /**
     * Handle admin authorization check
     *
     * @return mixed
     * @throws AuthorizationException
     */
    public function handle()
    {
        if (!$this->isAdmin()) {
            throw new AuthorizationException('You do not have permission to access this resource');
        }

        return null;
    }

    /**
     * Check if user is admin
     *
     * @return bool
     */
    protected function isAdmin(): bool
    {
        if (!isset($_SESSION['admin_user'])) {
            return false;
        }

        $userId = (int) ($_SESSION['admin_user']['id'] ?? 0);
        if ($userId <= 0) {
            return false;
        }

        return (new AuthorizationService(new AdminUserRepository(DatabaseFactory::make())))
            ->isSuperAdmin($userId);
    }
}
