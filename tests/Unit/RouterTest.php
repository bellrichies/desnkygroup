<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Router;
use App\Route;

/**
 * RouterTest - Unit tests for Router class
 */
class RouterTest extends TestCase
{
    /**
     * @var Router
     */
    private Router $router;

    protected function setUp(): void
    {
        $this->router = new Router();
    }

    /**
     * Test registering a GET route
     */
    public function testRegisterGetRoute(): void
    {
        $this->router->get('/test', 'TestController@index');
        
        $routes = $this->router->getRoutes();
        $this->assertCount(1, $routes);
        $this->assertInstanceOf(Route::class, $routes[0]);
    }

    /**
     * Test registering a POST route
     */
    public function testRegisterPostRoute(): void
    {
        $this->router->post('/test', 'TestController@store');
        
        $routes = $this->router->getRoutes();
        $this->assertCount(1, $routes);
    }

    /**
     * Test route grouping
     */
    public function testRouteGrouping(): void
    {
        $this->router->group('/admin', function ($router) {
            $router->get('/dashboard', 'Admin\DashboardController@index');
            $router->get('/users', 'Admin\UserController@index');
        });
        
        $routes = $this->router->getRoutes();
        $this->assertCount(2, $routes);
        $this->assertStringContainsString('/admin', $routes[0]->getPath());
    }

    /**
     * Test route matching
     */
    public function testRouteMatching(): void
    {
        $this->router->get('/', 'HomeController@index');
        $this->router->get('/products/{id}', 'ProductController@show');
        
        $match = $this->router->match('GET', '/');
        $this->assertIsArray($match);
        $this->assertArrayHasKey('route', $match);
        $this->assertArrayHasKey('parameters', $match);
    }

    /**
     * Test route matching with parameters
     */
    public function testRouteMatchingWithParameters(): void
    {
        $this->router->get('/products/{id}', 'ProductController@show');
        
        $match = $this->router->match('GET', '/products/123');
        $this->assertIsArray($match);
        $this->assertArrayHasKey('parameters', $match);
        $this->assertEquals('123', $match['parameters']['id']);
    }

    /**
     * Test route not matching
     */
    public function testRouteNotMatching(): void
    {
        $this->router->get('/', 'HomeController@index');
        
        $match = $this->router->match('GET', '/nonexistent');
        $this->assertNull($match);
    }

    /**
     * Test route with middleware
     */
    public function testRouteWithMiddleware(): void
    {
        $route = $this->router->get('/admin', 'AdminController@index');
        $route->middleware(['auth']);
        
        $middleware = $route->getMiddleware();
        $this->assertContains('auth', $middleware);
    }
}
