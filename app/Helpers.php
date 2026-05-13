<?php

/**
 * Escape a value for safe HTML output.
 *
 * @param mixed $value Value to escape.
 * @return string
 */
function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/**
 * Build an absolute application path.
 *
 * @param string $path Relative path.
 * @return string
 */
function base_path(string $path = ''): string
{
    $base = defined('BASE_PATH') ? BASE_PATH : dirname(__DIR__);
    return rtrim($base . '/' . ltrim($path, '/'), '/');
}

/**
 * Read an environment variable.
 *
 * @param string $key Environment key.
 * @param mixed $default Default value.
 * @return mixed
 */
function env_value(string $key, $default = null)
{
    return $_ENV[$key] ?? getenv($key) ?: $default;
}
