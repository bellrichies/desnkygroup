<?php

return [
    'default' => env('MAIL_MAILER', 'smtp'),
    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'noreply@desnkygroup.com'),
        'name' => env('MAIL_FROM_NAME', 'Desnky Global Resources'),
    ],
    'mailers' => [
        'smtp' => [
            'host' => env('MAIL_HOST', 'localhost'),
            'port' => (int) env('MAIL_PORT', 587),
            'username' => env('MAIL_USERNAME', ''),
            'password' => env('MAIL_PASSWORD', ''),
            'encryption' => env('MAIL_ENCRYPTION', 'tls'),
        ],
    ],
];
