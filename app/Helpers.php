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
 * Escape a value for safe HTML attribute output.
 *
 * @param mixed $value Value to escape.
 * @return string
 */
function e_attr($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Escape and normalize a URL for link and image attributes.
 *
 * @param mixed $value URL value.
 * @return string
 */
function e_url($value): string
{
    $url = trim((string) $value);

    if (preg_match('/^\s*javascript:/i', $url) === 1) {
        return '#';
    }

    return e_attr(filter_var($url, FILTER_SANITIZE_URL));
}

/**
 * Encode a value safely for JavaScript context.
 *
 * @param mixed $value Value to encode.
 * @return string
 */
function e_js($value): string
{
    return (string) json_encode($value, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
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
