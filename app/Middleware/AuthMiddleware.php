<?php

namespace App\Middleware;

/**
 * AuthMiddleware - Protects admin routes with session authentication.
 */
class AuthMiddleware extends Middleware
{
    private const SESSION_TIMEOUT_SECONDS = 3600;

    /**
     * Validate admin authentication and session freshness.
     *
     * @return string|null
     */
    public function handle(): ?string
    {
        if (!$this->hasValidSession()) {
            unset($_SESSION['admin_user'], $_SESSION['admin_last_activity'], $_SESSION['admin_authenticated_at']);

            if ($this->expectsJson()) {
                http_response_code(401);
                header('Content-Type: application/json');
                return (string) json_encode([
                    'success' => false,
                    'message' => 'Authentication required.',
                ]);
            }

            header('Location: /admin/login', true, 302);
            return '';
        }

        $_SESSION['admin_last_activity'] = time();

        return null;
    }

    private function hasValidSession(): bool
    {
        if (empty($_SESSION['admin_user']) || !is_array($_SESSION['admin_user'])) {
            return false;
        }

        $lastActivity = (int) ($_SESSION['admin_last_activity'] ?? 0);

        return $lastActivity > 0 && (time() - $lastActivity) <= self::SESSION_TIMEOUT_SECONDS;
    }

    private function expectsJson(): bool
    {
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
        $requestedWith = $_SERVER['HTTP_X_REQUESTED_WITH'] ?? '';

        return str_contains($accept, 'application/json')
            || strtolower($requestedWith) === 'xmlhttprequest';
    }
}
