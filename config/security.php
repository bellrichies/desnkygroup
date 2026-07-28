<?php

return [
    'csrf' => [
        'enabled' => (bool) env('CSRF_PROTECTION', true),
        'field' => '_token',
        'header' => 'HTTP_X_CSRF_TOKEN',
    ],
    'session' => [
        'secure' => (bool) env('SESSION_SECURE', false),
        'http_only' => true,
        'same_site' => 'Lax',
    ],
    'uploads' => [
        'allowed_extensions' => ['jpg', 'jpeg', 'png', 'webp'],
        'allowed_mime_types' => ['image/jpeg', 'image/png', 'image/webp'],
        'max_size' => 5 * 1024 * 1024,
    ],
    'headers' => [
        'hsts' => (bool) env('FORCE_HTTPS', false),
    ],
    'rate_limit' => [
        'enabled' => (bool) env('RATE_LIMIT_ENABLED', true),
        'max_attempts' => (int) env('RATE_LIMIT_MAX_ATTEMPTS', 120),
        'window_seconds' => (int) env('RATE_LIMIT_WINDOW_SECONDS', 60),
    ],
];
