<?php

namespace App\Middleware;

use App\Config;
use App\Exceptions\ThrottleRequestsException;

/**
 * File-backed rate limiting for public form/API abuse protection.
 */
class RateLimitMiddleware extends Middleware
{
    /**
     * @var array<int, string>
     */
    private array $parameters = [];

    /**
     * @param array<int, string> $parameters Optional "limit,windowSeconds" override.
     * @return void
     */
    public function setParameters(array $parameters): void
    {
        $this->parameters = $parameters;
    }

    /**
     * @throws ThrottleRequestsException
     */
    public function handle()
    {
        if ((bool) Config::get('security.rate_limit.enabled', true) === false) {
            return null;
        }

        [$limit, $window] = $this->limits();
        $key = $this->key($window);
        $path = $this->path($key);
        $attempts = $this->read($path);
        $now = time();

        $attempts = array_values(array_filter(
            $attempts,
            static fn (int $timestamp): bool => $timestamp > ($now - $window)
        ));

        if (count($attempts) >= $limit) {
            header('Retry-After: ' . $this->retryAfter($attempts, $window, $now));
            throw new ThrottleRequestsException();
        }

        $attempts[] = $now;
        $this->write($path, $attempts);

        return null;
    }

    /**
     * @return array{0: int, 1: int}
     */
    private function limits(): array
    {
        $limit = (int) ($this->parameters[0] ?? Config::get('security.rate_limit.max_attempts', 60));
        $window = (int) ($this->parameters[1] ?? Config::get('security.rate_limit.window_seconds', 60));

        return [max(1, $limit), max(1, $window)];
    }

    private function key(int $window): string
    {
        $ip = (string) ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0');
        $method = (string) ($_SERVER['REQUEST_METHOD'] ?? 'GET');
        $uri = parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH) ?: '/';

        return hash('sha256', "{$ip}|{$method}|{$uri}|{$window}");
    }

    private function path(string $key): string
    {
        $directory = base_path('storage/cache/rate_limits');

        if (!is_dir($directory)) {
            mkdir($directory, 0775, true);
        }

        return $directory . '/' . $key . '.json';
    }

    /**
     * @return array<int, int>
     */
    private function read(string $path): array
    {
        if (!is_file($path)) {
            return [];
        }

        $data = json_decode((string) file_get_contents($path), true);

        return is_array($data) ? array_map('intval', $data) : [];
    }

    /**
     * @param array<int, int> $attempts
     */
    private function write(string $path, array $attempts): void
    {
        file_put_contents($path, json_encode($attempts), LOCK_EX);
    }

    /**
     * @param array<int, int> $attempts
     */
    private function retryAfter(array $attempts, int $window, int $now): int
    {
        sort($attempts);

        return max(1, ($attempts[0] + $window) - $now);
    }
}
