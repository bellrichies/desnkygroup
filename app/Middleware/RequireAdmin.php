<?php

namespace App\Middleware;

use App\Exceptions\AuthorizationException;

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

        $user = $_SESSION['admin_user'];
        if (is_array($user)) {
            return ($user['role'] ?? null) === 'super_admin';
        }

        return isset($user->role) && $user->role === 'super_admin';
    }
}
