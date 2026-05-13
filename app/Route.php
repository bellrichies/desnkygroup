<?php

namespace App;

/**
 * Route - Represents a single HTTP route
 *
 * Stores route configuration including method, path, controller action, and middleware.
 * Handles route parameter extraction and matching logic.
 */
class Route
{
    /**
     * @var string HTTP method (GET, POST, PUT, PATCH, DELETE)
     */
    private string $method;

    /**
     * @var string Route path pattern (e.g., '/products/{id}')
     */
    private string $path;

    /**
     * @var string|callable Controller action or callable
     */
    private $action;

    /**
     * @var array Middleware classes to apply to this route
     */
    private array $middleware;

    /**
     * @var array Route parameters from path
     */
    private array $parameters = [];

    /**
     * Constructor
     *
     * @param string $method HTTP method
     * @param string $path Route path
     * @param string|callable $action Controller action
     * @param array $middleware Middleware classes
     */
    public function __construct(string $method, string $path, $action, array $middleware = [])
    {
        $this->method = strtoupper($method);
        $this->path = $path;
        $this->action = $action;
        $this->middleware = $middleware;
    }

    /**
     * Check if route matches incoming request
     *
     * @param string $method HTTP method
     * @param string $uri Request URI
     * @return array|null Array with 'route' and 'parameters' keys, or null if no match
     */
    public function matches(string $method, string $uri): ?array
    {
        if (strtoupper($method) !== $this->method) {
            return null;
        }

        // Convert path pattern to regex
        $pattern = $this->pathToRegex($this->path);

        if (!@preg_match($pattern, $uri, $matches)) {
            return null;
        }

        // Extract parameters from regex matches
        $parameters = [];
        $paramNames = $this->extractParameterNames($this->path);

        foreach ($paramNames as $index => $name) {
            $parameters[$name] = $matches[$index + 1] ?? null;
        }

        return [
            'route' => $this,
            'parameters' => $parameters,
        ];
    }

    /**
     * Convert path pattern to regex
     *
     * Converts patterns like '/products/{id}' to '/products/(\d+)' regex
     *
     * @param string $path Path pattern
     * @return string Regex pattern
     */
    private function pathToRegex(string $path): string
    {
        $pattern = preg_replace_callback(
            '/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/',
            static fn (array $matches): string => '___ROUTE_PARAM_' . $matches[1] . '___',
            $path
        );
        $pattern = preg_quote((string) $pattern, '/');
        $pattern = preg_replace('/___ROUTE_PARAM_[a-zA-Z_][a-zA-Z0-9_]*___/', '([^/]+)', $pattern);

        return '~^' . $pattern . '$~';
    }

    /**
     * Extract parameter names from path pattern
     *
     * @param string $path Path pattern
     * @return array Parameter names in order
     */
    private function extractParameterNames(string $path): array
    {
        preg_match_all('/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/', $path, $matches);
        return $matches[1];
    }
    /**
     * Add middleware to route
     *
     * @param string|array $middleware Middleware class name(s)
     * @return self
     */
    public function middleware($middleware): self
    {
        $middleware = is_array($middleware) ? $middleware : [$middleware];
        $this->middleware = array_merge($this->middleware, $middleware);
        return $this;
    }

    /**
     * Get HTTP method
     *
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Get route path
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Get controller action
     *
     * @return string|callable
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Get middleware
     *
     * @return array
     */
    public function getMiddleware(): array
    {
        return $this->middleware;
    }

    /**
     * Get route parameters
     *
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }
}
