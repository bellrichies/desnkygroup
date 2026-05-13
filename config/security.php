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
        'max_size' => 5 * 1024 * 1024,
    ],
    'headers' => [
        'hsts' => (bool) env('FORCE_HTTPS', false),
    ],
];
