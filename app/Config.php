<?php

namespace App;

use App\Exceptions\ConfigurationException;

/**
 * Config - Centralized configuration management
 *
 * Loads configuration files, supports dot notation access, and caches values.
 * Merges .env variables with PHP configuration files.
 */
class Config
{
    /**
     * @var array All loaded configuration
     */
    private static array $config = [];

    /**
     * @var bool Whether configuration is loaded
     */
    private static bool $loaded = false;

    /**
     * Load all configuration files
     *
     * @param string $configPath Path to config directory
     * @return void
     * @throws ConfigurationException
     */
    public static function load(string $configPath): void
    {
        if (self::$loaded) {
            return;
        }

        $configPath = rtrim($configPath, '/\\');

        if (!is_dir($configPath)) {
            throw new ConfigurationException("Config directory not found: {$configPath}");
        }

        // Load all PHP config files
        foreach (glob($configPath . '/*.php') as $file) {
            $name = basename($file, '.php');
            self::$config[$name] = require $file;
        }

        self::$loaded = true;
    }

    /**
     * Get configuration value using dot notation
     *
     * Example: Config::get('database.host')
     *
     * @param string $key Configuration key (dot notation)
     * @param mixed $default Default value if key not found
     * @return mixed Configuration value or default
     */
    public static function get(string $key, $default = null)
    {
        $keys = explode('.', $key);
        $value = self::$config;
        foreach ($keys as $k) {
            if (is_array($value) && isset($value[$k])) {
                $value = $value[$k];
            } else {
                return $default;
            }
        }

        return $value;
    }

    /**
     * Set configuration value using dot notation
     *
     * @param string $key Configuration key (dot notation)
     * @param mixed $value Value to set
     * @return void
     */
    public static function set(string $key, $value): void
    {
        $keys = explode('.', $key);
        $current = &self::$config;
        foreach ($keys as $k) {
            if (!isset($current[$k])) {
                $current[$k] = [];
            }
            $current = &$current[$k];
        }

        $current = $value;
    }

    /**
     * Merge configuration values into an existing group.
     *
     * @param string $key Configuration key.
     * @param array $values Values to merge.
     * @return void
     */
    public static function merge(string $key, array $values): void
    {
        $existing = self::get($key, []);
        self::set($key, array_replace_recursive((array) $existing, $values));
    }
    /**
     * Get all configuration
     *
     * @return array
     */
    public static function all(): array
    {
        return self::$config;
    }

    /**
     * Check if configuration key exists
     *
     * @param string $key Configuration key
     * @return bool
     */
    public static function has(string $key): bool
    {
        return self::get($key) !== null;
    }

    /**
     * Reset all configuration
     *
     * @return void
     */
    public static function reset(): void
    {
        self::$config = [];
        self::$loaded = false;
    }
}
