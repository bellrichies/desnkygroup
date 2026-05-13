<?php

namespace App;

/**
 * Router - Handles HTTP routing for the application
 *
 * Manages route registration, matching, and dispatching for GET, POST, PUT, PATCH, DELETE methods.
 * Supports route parameters, middleware attachment, and route grouping by prefix.
 */
class Router
{
    /**
     * @var array Registered routes
     */
    private array $routes = [];

    /**
     * @var string Current route group prefix
     */
    private string $currentPrefix = '';

    /**
     * @var array Middleware stack for group
     */
    private array $currentMiddleware = [];
/**
     * @var array<string, class-string> Middleware aliases.
     */
    private array $middlewareAliases = [
        'auth' => \App\Middleware\AuthenticateUser::class,
        'admin' => \App\Middleware\RequireAdmin::class,
        'csrf' => \App\Middleware\VerifyCsrfToken::class,
        'security' => \App\Middleware\SecurityHeaders::class,
    ];
    /**
     * Register a GET route
     *
     * @param string $path Route path (e.g., '/products/{id}')
     * @param string|array $action Controller action (e.g., 'ProductController@show')
     * @param array $middleware Middleware to apply
     * @return Route
     */
    public function get(string $path, $action, array $middleware = []): Route
    {
        return $this->registerRoute('GET', $path, $action, $middleware);
    }

    /**
     * Register a POST route
     *
     * @param string $path Route path
     * @param string|array $action Controller action
     * @param array $middleware Middleware to apply
     * @return Route
     */
    public function post(string $path, $action, array $middleware = []): Route
    {
        return $this->registerRoute('POST', $path, $action, $middleware);
    }

    /**
     * Register a PUT route
     *
     * @param string $path Route path
     * @param string|array $action Controller action
     * @param array $middleware Middleware to apply
     * @return Route
     */
    public function put(string $path, $action, array $middleware = []): Route
    {
        return $this->registerRoute('PUT', $path, $action, $middleware);
    }

    /**
     * Register a PATCH route
     *
     * @param string $path Route path
     * @param string|array $action Controller action
     * @param array $middleware Middleware to apply
     * @return Route
     */
    public function patch(string $path, $action, array $middleware = []): Route
    {
        return $this->registerRoute('PATCH', $path, $action, $middleware);
    }

    /**
     * Register a DELETE route
     *
     * @param string $path Route path
     * @param string|array $action Controller action
     * @param array $middleware Middleware to apply
     * @return Route
     */
    public function delete(string $path, $action, array $middleware = []): Route
    {
        return $this->registerRoute('DELETE', $path, $action, $middleware);
    }

    /**
     * Group routes with a common prefix and middleware
     *
     * @param string $prefix Route prefix (e.g., '/admin')
     * @param callable $callback Callback to register routes within group
     * @param array $middleware Middleware to apply to all routes in group
     * @return void
     */
    public function group(string $prefix, callable $callback, array $middleware = []): void
    {
        $previousPrefix = $this->currentPrefix;
        $previousMiddleware = $this->currentMiddleware;

        $this->currentPrefix = $previousPrefix . $prefix;
        $this->currentMiddleware = array_merge($previousMiddleware, $middleware);

        call_user_func($callback, $this);

        $this->currentPrefix = $previousPrefix;
        $this->currentMiddleware = $previousMiddleware;
    }

    /**
     * Register a middleware alias.
     *
     * @param string $alias Short alias used in routes.
     * @param class-string $class Middleware class name.
     * @return void
     */
    public function aliasMiddleware(string $alias, string $class): void
    {
        $this->middlewareAliases[$alias] = $class;
    }
    /**
     * Register a route
     *
     * @param string $method HTTP method
     * @param string $path Route path
     * @param string|array $action Controller action
     * @param array $middleware Middleware to apply
     * @return Route
     */
    private function registerRoute(string $method, string $path, $action, array $middleware = []): Route
    {
        $fullPath = $this->currentPrefix . $path;
        $allMiddleware = array_merge($this->currentMiddleware, $middleware);

        $route = new Route($method, $fullPath, $action, $allMiddleware);
        $this->routes[] = $route;

        return $route;
    }

    /**
     * Match incoming request to a registered route
     *
     * @param string $method HTTP method
     * @param string $uri Request URI
     * @return array|null Array with route and parameters, or null if no match
     */
    public function match(string $method, string $uri): ?array
    {
        // Remove query string from URI
        $uri = parse_url($uri, PHP_URL_PATH);

        foreach ($this->routes as $route) {
            $routeData = $route->matches($method, $uri);
            if ($routeData !== null) {
                return $routeData;
            }
        }

        return null;
    }

    /**
     * Get all registered routes
     *
     * @return array
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }

    /**
     * Dispatch a matched route
     *
     * @param array $routeData Matched route data from match()
     * @param Container $container DI container for dependency resolution
     * @return mixed Route result/response
     * @throws Exceptions\NotFoundException
     */
    public function dispatch(array $routeData, Container $container)
    {
        $route = $routeData['route'];
        $parameters = $routeData['parameters'];

        // Process middleware
        foreach ($route->getMiddleware() as $middlewareClass) {
            $middlewareClass = $this->resolveMiddlewareClass((string) $middlewareClass);
            $middleware = $container->make($middlewareClass);
            $response = $middleware->handle();
            if ($response !== null) {
                return $response;
            }
        }
        // Extract controller and action
        $action = $route->getAction();
        if (is_string($action)) {
            [$controller, $method] = explode('@', $action);
        } else {
            // Handle callable action
            return $container->call($action, $parameters);
        }

        // Resolve controller from container
        $controllerClass = 'App\\Controllers\\' . $controller;
        if (!class_exists($controllerClass)) {
            throw new Exceptions\NotFoundException("Controller {$controllerClass} not found");
        }

        $controllerInstance = $container->make($controllerClass);

        // Call controller method with parameters
        if (!method_exists($controllerInstance, $method)) {
            throw new Exceptions\NotFoundException("Method {$method} not found in {$controllerClass}");
        }

        return $controllerInstance->{$method}(...array_values($parameters));
    }

    /**
     * Resolve a middleware alias or class name.
     *
     * @param string $middleware Middleware alias or class name.
     * @return string
     */
    private function resolveMiddlewareClass(string $middleware): string
    {
        return $this->middlewareAliases[$middleware] ?? $middleware;
    }
}
