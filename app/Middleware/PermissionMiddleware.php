<?php

namespace App\Middleware;

use App\Exceptions\AuthorizationException;
use App\Repositories\AdminUserRepository;
use App\Services\AuthorizationService;
use App\Support\DatabaseFactory;

/**
 * Enforces a required permission on a route.
 */
class PermissionMiddleware extends Middleware
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
        $permission = $this->parameters[0] ?? '';
        $userId = (int) ($_SESSION['admin_user']['id'] ?? 0);

        if ($permission === '' || $userId <= 0) {
            throw new AuthorizationException('Permission denied.');
        }

        $authorization = new AuthorizationService(new AdminUserRepository(DatabaseFactory::make()));
        if (!$authorization->hasPermission($userId, $permission)) {
            throw new AuthorizationException('You do not have permission to access this resource.');
        }

        return null;
    }
}
