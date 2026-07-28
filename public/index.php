<?php

/**
 * Desnky Global Resources Website
 * Main entry point for all HTTP requests
 * 
 * All requests are routed through this file via Apache/Nginx rewrite rules.
 */

// Define base path
define('BASE_PATH', dirname(__DIR__));
define('PUBLIC_PATH', __DIR__);
define('STORAGE_PATH', BASE_PATH . '/storage');
define('LOGS_PATH', STORAGE_PATH . '/logs');

// Start session with secure defaults before application code writes session data.
$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (($_SERVER['SERVER_PORT'] ?? null) === '443');

session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => $isHttps,
    'httponly' => true,
    'samesite' => 'Lax',
]);

session_start();

// Load Composer autoloader
require_once BASE_PATH . '/vendor/autoload.php';

// Load .env file
$dotenv = Dotenv\Dotenv::createImmutable(BASE_PATH);
$dotenv->load();

// Load configuration
\App\Config::load(BASE_PATH . '/config');

$debug = (bool) \App\Config::get('app.debug', false);
error_reporting(E_ALL);
ini_set('display_errors', $debug ? '1' : '0');
ini_set('log_errors', '1');

$handler = new \App\ExceptionHandler();
$handler->register();

try {
    // Initialize container
    $container = new \App\Container();

    $container->singleton(\App\Logger::class, function () {
        $configuredPath = (string) \App\Config::get('logging.channels.file.path', 'storage/logs/app.log');
        $path = str_starts_with($configuredPath, BASE_PATH)
            ? $configuredPath
            : BASE_PATH . '/' . ltrim($configuredPath, '/');

        return new \App\Logger($path);
    });

    // Register services
    $container->singleton('database', function () {
        $config = \App\Config::get('database.connections.mysql');
        return new \App\Database\Connection($config);
    });

    $container->singleton(\App\Database\Connection::class, function ($c) {
        return $c->get('database');
    });

    $container->register(\App\Database\QueryBuilder::class, function ($c) {
        return new \App\Database\QueryBuilder($c->get(\App\Database\Connection::class));
    });

    $container->singleton(\App\Security\Hasher::class, function () {
        return new \App\Security\Hasher();
    });

    $container->singleton(\App\Security\Encrypter::class, function () {
        return new \App\Security\Encrypter((string) \App\Config::get('app.key', ''));
    });

    $container->singleton(\App\Services\CacheService::class, function () {
        return new \App\Services\CacheService();
    });

    $container->singleton('router', function () {
        return new \App\Router();
    });

    $container->make(\App\Middleware\SecurityHeaders::class)->handle();
    $container->make(\App\Middleware\RateLimitMiddleware::class)->handle();

    // Load routes
    $router = $container->get('router');
    require_once BASE_PATH . '/routes/web.php';

    // Get request method and URI
    $method = $_SERVER['REQUEST_METHOD'];
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

    // Match route
    $routeMatch = $router->match($method, $uri);

    if ($routeMatch === null) {
        throw new \App\Exceptions\NotFoundException('Page not found');
    }

    // Dispatch route
    $response = $router->dispatch($routeMatch, $container);

    if ($response !== null) {
        echo $response;
    }
} catch (\Throwable $e) {
    $handler->render($e);
}
