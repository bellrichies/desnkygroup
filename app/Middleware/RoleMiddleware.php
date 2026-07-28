<?php

namespace App\Middleware;

use App\Exceptions\AuthorizationException;
use App\Repositories\AdminUserRepository;
use App\Services\AuthorizationService;
use App\Support\DatabaseFactory;

/**
 * Enforces a required admin role on a route.
 */
class RoleMiddleware extends Middleware
{
    /** @var array<int, string> */
    private array $parameters = [];

    /**
     * @param array<int, string> $parameters Middleware parameters.
     * @return void
     */
    public function setParameters(array $parameters): void
    {
        $this->parameters = $parameters;
    }

    public function handle()
    {
        $role = $this->parameters[0] ?? '';
        $userId = (int) ($_SESSION['admin_user']['id'] ?? 0);

        if ($role === '' || $userId <= 0) {
            throw new AuthorizationException('Role required.');
        }

        $authorization = new AuthorizationService(new AdminUserRepository(DatabaseFactory::make()));
        if (!$authorization->hasRole($userId, $role)) {
            throw new AuthorizationException('You do not have the required role.');
        }

        return null;
    }
}
