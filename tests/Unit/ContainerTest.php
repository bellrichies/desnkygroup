<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Container;

/**
 * ContainerTest - Unit tests for Container class
 */
class ContainerTest extends TestCase
{
    /**
     * @var Container
     */
    private Container $container;

    protected function setUp(): void
    {
        $this->container = new Container();
    }

    /**
     * Test registering a service
     */
    public function testRegisterService(): void
    {
        $this->container->register('test', function () {
            return 'test_value';
        });

        $this->assertTrue($this->container->has('test'));
    }

    /**
     * Test resolving a service
     */
    public function testResolveService(): void
    {
        $this->container->register('test', function () {
            return 'test_value';
        });

        $result = $this->container->make('test');
        $this->assertEquals('test_value', $result);
    }

    /**
     * Test singleton service
     */
    public function testSingletonService(): void
    {
        $this->container->singleton('test', function () {
            return new \stdClass();
        });

        $first = $this->container->make('test');
        $second = $this->container->make('test');

        $this->assertSame($first, $second);
    }

    /**
     * Test automatic dependency injection
     */
    public function testAutomaticDependencyInjection(): void
    {
        $this->container->register('dependency', function () {
            return new \stdClass();
        });

        $instance = $this->container->make(TestService::class);
        $this->assertInstanceOf(TestService::class, $instance);
    }

    /**
     * Test service not found
     */
    public function testServiceNotFound(): void
    {
        $this->expectException(\Exception::class);
        $this->container->make('nonexistent');
    }

    /**
     * Test circular dependency detection
     */
    public function testCircularDependencyDetection(): void
    {
        $this->container->register('circular_a', function ($c) {
            return $c->make('circular_b');
        });

        $this->container->register('circular_b', function ($c) {
            return $c->make('circular_a');
        });

        $this->expectException(\Exception::class);
        $this->container->make('circular_a');
    }
}

/**
 * Test service class
 */
class TestService
{
    public function __construct()
    {
    }
}
