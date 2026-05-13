<?php

/**
 * Database Configuration
 * 
 * Database connection settings and driver configuration
 */

return [
    'default' => env('DB_DRIVER', 'mysql'),

    'connections' => [
        'mysql' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', 3306),
            'database' => env('DB_DATABASE', 'desnkygroup'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => env('DB_CHARSET', 'utf8mb4'),
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
        ],
    ],

    // Migration settings
    'migrations' => [
        'table' => 'migrations',
        'directory' => 'database/migrations',
    ],

    // Query settings
    'query' => [
        'timeout' => 30, // seconds
        'slow_query_log' => env('DB_SLOW_QUERY_LOG', false),
        'slow_query_threshold' => 1000, // milliseconds
    ],

    // Connection pooling (future Redis support)
    'redis' => [
        'client' => 'predis',
        'default' => [
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', 6379),
            'database' => env('REDIS_CACHE_DB', 1),
        ],
    ],
];
