<?php

/**
 * Application Configuration
 * 
 * Core application settings and environment constants
 */

/**
 * Helper function to get environment variable
 * 
 * @param string $key Environment variable key
 * @param mixed $default Default value
 * @return mixed
 */
if (!function_exists('env')) {
    function env(string $key, $default = null)
    {
        return $_ENV[$key] ?? getenv($key) ?: $default;
    }
}

return [
    'name' => env('APP_NAME', 'Desnky Global Resources'),
    'env' => env('APP_ENV', 'production'),
    'debug' => env('APP_DEBUG', false),
    'key' => env('APP_KEY', ''),
    'url' => env('APP_URL', 'http://localhost'),
    'timezone' => env('APP_TIMEZONE', 'UTC'),
    
    // Application settings
    'locale' => 'en_US',
    'charset' => 'UTF-8',
    'locale_fallback' => 'en',

    // Session configuration
    'session' => [
        'driver' => 'file',
        'lifetime' => 120, // minutes
        'expire_on_close' => false,
        'encrypt' => false,
        'cookie_name' => 'DESNKY_SESSION',
        'cookie_path' => '/',
        'cookie_domain' => null,
        'cookie_secure' => env('SESSION_SECURE', false),
        'cookie_http_only' => true,
        'cookie_same_site' => 'Lax',
    ],

    // Security settings
    'security' => [
        'hash_algorithm' => 'bcrypt',
        'bcrypt_rounds' => 12,
        'max_login_attempts' => 5,
        'lock_timeout' => 15, // minutes
    ],

    // API settings
    'api' => [
        'version' => 'v1',
        'rate_limit' => 100, // requests per minute
        'pagination_limit' => 50,
    ],
];
