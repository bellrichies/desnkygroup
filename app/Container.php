<?php

namespace App;

use ReflectionClass;
use ReflectionNamedType;
use ReflectionParameter;

/**
 * Container - Dependency Injection Container
 *
 * Manages service registration, resolution, and automatic constructor injection
 * based on type hints. Supports singleton services and callable factories.
 */
class Container
{
    /**
     * @var array Registered services
     */
    private array $services = [];

    /**
     * @var array Singleton instances
     */
    private array $singletons = [];

    /**
     * @var array Service metadata (is_singleton flag)
     */
    private array $metadata = [];

    /**
     * @var array Currently resolving services (for circular dependency detection)
     */
    private array $resolving = [];

    /**
     * Register a service with a factory callback
     *
     * @param string $key Service key/name
     * @param callable $callback Factory callback to resolve service
     * @return void
     */
    public function register(string $key, callable $callback): void
    {
        $this->services[$key] = $callback;
        $this->metadata[$key] = ['singleton' => false];
    }

    /**
     * Register a singleton service
     *
     * Only creates instance once, returns same instance on subsequent calls
     *
     * @param string $key Service key/name
     * @param callable $callback Factory callback to resolve service
     * @return void
     */
    public function singleton(string $key, callable $callback): void
    {
        $this->services[$key] = $callback;
        $this->metadata[$key] = ['singleton' => true];
    }

    /**
     * Resolve and instantiate a service
     *
     * Automatically injects dependencies based on constructor type hints.
     * Returns singleton instances if registered as singleton.
     *
     * @param string $key Service key or class name
     * @param array $params Additional parameters to pass
     * @return mixed Resolved service instance
     * @throws \Exception If circular dependency detected or cannot resolve
     */
    public function make(string $key, array $params = [])
    {
        // Check for circular dependencies
        if (isset($this->resolving[$key])) {
            throw new \Exception("Circular dependency detected for '{$key}'");
        }

        // Return existing singleton
        if (isset($this->singletons[$key])) {
            return $this->singletons[$key];
        }

        // Mark as resolving
        $this->resolving[$key] = true;

        try {
            $instance = $this->resolve($key, $params);

            // Cache if singleton
            if (isset($this->metadata[$key]) && $this->metadata[$key]['singleton']) {
                $this->singletons[$key] = $instance;
            }

            return $instance;
        } finally {
            unset($this->resolving[$key]);
        }
    }

    /**
     * Resolve a service
     *
     * @param string $key Service key or class name
     * @param array $params Additional parameters
     * @return mixed Resolved instance
     * @throws \Exception If cannot resolve
     */
    private function resolve(string $key, array $params = [])
    {
        // Use registered factory if available
        if (isset($this->services[$key])) {
            return call_user_func($this->services[$key], $this, $params);
        }

        // Try to instantiate class by name
        if (!class_exists($key)) {
            throw new \Exception("Service '{$key}' not found in container and class does not exist");
        }

        return $this->instantiate($key, $params);
    }

    /**
     * Instantiate a class with automatic constructor injection
     *
     * @param string $className Class name
     * @param array $params Additional parameters
     * @return object Class instance
     * @throws \Exception If cannot resolve dependencies
     */
    private function instantiate(string $className, array $params = []): object
    {
        $reflection = new ReflectionClass($className);

        // No constructor, create instance
        if (!$reflection->getConstructor()) {
            return new $className();
        }

        $constructor = $reflection->getConstructor();
        $dependencies = [];

        // Resolve constructor parameters
        foreach ($constructor->getParameters() as $parameter) {
            $dependencies[] = $this->resolveParameter($parameter, $params);
        }

        return new $className(...$dependencies);
    }

    /**
     * Resolve a single constructor parameter
     *
     * @param ReflectionParameter $parameter Parameter to resolve
     * @param array $params Additional parameters
     * @return mixed Resolved parameter value
     * @throws \Exception If cannot resolve
     */
    private function resolveParameter(ReflectionParameter $parameter, array $params = [])
    {
        $name = $parameter->getName();

        // Check provided parameters first
        if (isset($params[$name])) {
            return $params[$name];
        }

        // Check type hint
        if ($parameter->hasType()) {
            $type = $parameter->getType();
            if (!$type instanceof ReflectionNamedType) {
                if ($parameter->isDefaultValueAvailable()) {
                    return $parameter->getDefaultValue();
                }

                throw new \Exception("Cannot resolve union type for parameter '{$name}'");
            }

            // Handle built-in types
            if ($type->isBuiltin()) {
                if ($parameter->isDefaultValueAvailable()) {
                    return $parameter->getDefaultValue();
                }
                throw new \Exception("Cannot resolve built-in type '{$type}' for parameter '{$name}'");
            }

            // Try to resolve typed class
            $typeName = $type->getName();
            if (class_exists($typeName)) {
                return $this->make($typeName);
            }
        }

        // Use default value if available
        if ($parameter->isDefaultValueAvailable()) {
            return $parameter->getDefaultValue();
        }

        throw new \Exception("Cannot resolve parameter '{$name}' for constructor");
    }

    /**
     * Call a callable with automatic dependency injection
     *
     * @param callable $callback Callable to invoke
     * @param array $params Additional parameters
     * @return mixed Result of callable
     * @throws \Exception If cannot resolve dependencies
     */
    public function call(callable $callback, array $params = [])
    {
        if (!is_array($callback) && !$callback instanceof \Closure) {
            // Handle string function names
            $reflection = new \ReflectionFunction($callback);
        } else {
            // Handle closures and class methods
            if ($callback instanceof \Closure) {
                $reflection = new \ReflectionFunction($callback);
            } else {
                // Class method: [$class, 'method']
                [$class, $method] = $callback;
                $reflection = new \ReflectionMethod($class, $method);
            }
        }

        $dependencies = [];
        foreach ($reflection->getParameters() as $parameter) {
            $dependencies[] = $this->resolveParameter($parameter, $params);
        }

        return call_user_func_array($callback, $dependencies);
    }

    /**
     * Check if service is registered
     *
     * @param string $key Service key
     * @return bool
     */
    public function has(string $key): bool
    {
        return isset($this->services[$key]) || isset($this->singletons[$key]);
    }

    /**
     * Get a service as alias for make()
     *
     * @param string $key Service key
     * @param array $params Additional parameters
     * @return mixed Resolved service
     */
    public function get(string $key, array $params = [])
    {
        return $this->make($key, $params);
    }

    /**
     * Clear all registered services
     *
     * @return void
     */
    public function clear(): void
    {
        $this->services = [];
        $this->singletons = [];
        $this->metadata = [];
        $this->resolving = [];
    }
}
