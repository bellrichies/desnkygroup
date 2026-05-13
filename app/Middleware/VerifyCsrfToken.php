<?php

namespace App\Middleware;

use App\Config;
use App\Exceptions\TokenMismatchException;

/**
 * VerifyCsrfToken - CSRF token verification middleware
 *
 * Validates CSRF tokens on state-changing requests (POST, PUT, PATCH, DELETE).
 */
class VerifyCsrfToken extends Middleware
{
    /**
     * Routes that skip CSRF verification
     *
     * @var array
     */
    protected array $except = [
        '/api/webhook/*',
    ];

    /**
     * Handle CSRF verification
     *
     * @return mixed
     * @throws TokenMismatchException
     */
    public function handle()
    {
        if ((bool) Config::get('security.csrf.enabled', true) === false) {
            return null;
        }

        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        // Only verify for state-changing methods
        if (!in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            return null;
        }

        // Check if route is in except list
        if ($this->shouldSkip()) {
            return null;
        }

        // Get token from request
        $token = $this->getToken();

        if (!$token || !$this->verifyToken($token)) {
            throw new TokenMismatchException('CSRF token verification failed');
        }

        return null;
    }

    /**
     * Get CSRF token from request
     *
     * @return string|null
     */
    protected function getToken(): ?string
    {
        // Check POST data
        if (isset($_POST['_token'])) {
            return $_POST['_token'];
        }

        // Check JSON body
        $input = json_decode(file_get_contents('php://input'), true);
        if (isset($input['_token'])) {
            return $input['_token'];
        }

        // Check headers
        $header = (string) Config::get('security.csrf.header', 'HTTP_X_CSRF_TOKEN');
        if (isset($_SERVER[$header])) {
            return $_SERVER[$header];
        }
        return null;
    }

    /**
     * Verify CSRF token
     *
     * @param string $token Token to verify
     * @return bool
     */
    protected function verifyToken(string $token): bool
    {
        if (!isset($_SESSION['csrf_token'])) {
            return false;
        }

        return hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Check if route should skip CSRF verification
     *
     * @return bool
     */
    protected function shouldSkip(): bool
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '';

        foreach ($this->except as $pattern) {
            if ($this->matches($pattern, $uri)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if URI matches pattern
     *
     * @param string $pattern Pattern with wildcards
     * @param string $uri URI to check
     * @return bool
     */
    protected function matches(string $pattern, string $uri): bool
    {
        $pattern = str_replace('*', '.*', preg_quote($pattern));
        return preg_match("/^{$pattern}$/", $uri) === 1;
    }
}
