<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Config;

/**
 * ConfigTest - Unit tests for Config class
 */
class ConfigTest extends TestCase
{
    protected function setUp(): void
    {
        Config::reset();
    }

    /**
     * Test loading configuration
     */
    public function testLoadConfig(): void
    {
        Config::load(__DIR__ . '/../../config');
        
        // Check if config loaded (should have app config at least)
        $appName = Config::get('app.name');
        $this->assertIsString($appName);
    }

    /**
     * Test getting configuration value with dot notation
     */
    public function testGetConfigWithDotNotation(): void
    {
        Config::load(__DIR__ . '/../../config');
        
        $value = Config::get('app.name');
        $this->assertIsString($value);
    }

    /**
     * Test getting non-existent configuration
     */
    public function testGetNonExistentConfig(): void
    {
        Config::load(__DIR__ . '/../../config');
        
        $value = Config::get('nonexistent.key', 'default');
        $this->assertEquals('default', $value);
    }

    /**
     * Test setting configuration value
     */
    public function testSetConfig(): void
    {
        Config::set('custom.key', 'custom_value');
        
        $value = Config::get('custom.key');
        $this->assertEquals('custom_value', $value);
    }

    /**
     * Test checking if configuration key exists
     */
    public function testHasConfig(): void
    {
        Config::set('test.key', 'value');
        
        $this->assertTrue(Config::has('test.key'));
        $this->assertFalse(Config::has('nonexistent.key'));
    }

    /**
     * Test getting all configuration
     */
    public function testGetAllConfig(): void
    {
        Config::set('test1.key', 'value1');
        Config::set('test2.key', 'value2');
        
        $all = Config::all();
        $this->assertIsArray($all);
        $this->assertArrayHasKey('test1', $all);
    }
}
