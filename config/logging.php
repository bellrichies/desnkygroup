<?php

return [
    'default' => 'file',
    'channels' => [
        'file' => [
            'path' => env('LOG_FILE', 'storage/logs/app.log'),
            'level' => env('LOG_LEVEL', 'debug'),
        ],
    ],
];
