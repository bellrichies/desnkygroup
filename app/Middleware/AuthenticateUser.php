<?php

namespace App\Middleware;

use App\Exceptions\AuthorizationException;

/**
 * AuthenticateUser - Authentication middleware
 *
 * Ensures user is authenticated before accessing protected routes.
 * Redirects to login if not authenticated.
 */
class AuthenticateUser extends Middleware
{
    /**
     * Handle authentication check
     *
     * @return mixed
     * @throws AuthorizationException
     */
    public function handle()
    {
        if (!$this->isAuthenticated()) {
            throw new AuthorizationException('You must be logged in to access this page');
        }

        return null;
    }

    /**
     * Check if user is authenticated
     *
     * @return bool
     */
    protected function isAuthenticated(): bool
    {
        return isset($_SESSION['admin_user']) && $_SESSION['admin_user'] !== null;
    }
}
