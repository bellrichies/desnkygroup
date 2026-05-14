<?php

namespace App\Services;

/**
 * Lightweight file cache with tag invalidation for shared hosting deployments.
 */
class CacheService extends BaseService
{
    private string $directory;

    public function __construct(?string $directory = null)
    {
        $this->directory = $directory ?? base_path('storage/cache/data');

        if (!is_dir($this->directory)) {
            mkdir($this->directory, 0775, true);
        }
    }

    /**
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        $path = $this->path($key);

        if (!is_file($path)) {
            return $default;
        }

        $payload = json_decode((string) file_get_contents($path), true);

        if (!is_array($payload) || (int) ($payload['expires_at'] ?? 0) < time()) {
            @unlink($path);
            return $default;
        }

        return $payload['value'] ?? $default;
    }

    /**
     * @param mixed $value
     * @param array<int, string> $tags
     */
    public function put(string $key, $value, int $ttlSeconds = 300, array $tags = []): void
    {
        file_put_contents($this->path($key), json_encode([
            'key' => $key,
            'tags' => array_values($tags),
            'expires_at' => time() + max(1, $ttlSeconds),
            'value' => $value,
        ]), LOCK_EX);
    }

    /**
     * @param callable(): mixed $callback
     * @param array<int, string> $tags
     * @return mixed
     */
    public function remember(string $key, int $ttlSeconds, callable $callback, array $tags = [])
    {
        $cached = $this->get($key, '__cache_miss__');

        if ($cached !== '__cache_miss__') {
            return $cached;
        }

        $value = $callback();
        $this->put($key, $value, $ttlSeconds, $tags);

        return $value;
    }

    public function forget(string $key): void
    {
        @unlink($this->path($key));
    }

    public function flushTag(string $tag): void
    {
        foreach (glob($this->directory . '/*.json') ?: [] as $path) {
            $payload = json_decode((string) file_get_contents($path), true);
            $tags = is_array($payload) ? ($payload['tags'] ?? []) : [];

            if (is_array($tags) && in_array($tag, $tags, true)) {
                @unlink($path);
            }
        }
    }

    public function flushExpired(): int
    {
        $removed = 0;

        foreach (glob($this->directory . '/*.json') ?: [] as $path) {
            $payload = json_decode((string) file_get_contents($path), true);

            if (!is_array($payload) || (int) ($payload['expires_at'] ?? 0) < time()) {
                @unlink($path);
                $removed++;
            }
        }

        return $removed;
    }

    private function path(string $key): string
    {
        return $this->directory . '/' . hash('sha256', $key) . '.json';
    }
}
