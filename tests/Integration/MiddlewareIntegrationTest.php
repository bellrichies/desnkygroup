<?php

namespace Tests\Integration;

use App\Exceptions\ThrottleRequestsException;
use App\Exceptions\TokenMismatchException;
use App\Middleware\AuthMiddleware;
use App\Middleware\RateLimitMiddleware;
use App\Middleware\VerifyCsrfToken;
use PHPUnit\Framework\TestCase;

class MiddlewareIntegrationTest extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION = [];
        $_POST = [];
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/';
        $_SERVER['REMOTE_ADDR'] = '127.0.0.' . random_int(20, 199);
        $_SERVER['HTTP_ACCEPT'] = 'text/html';
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
        $_POST = [];
    }

    public function testAuthMiddlewareRedirectsUnauthenticatedAdmin(): void
    {
        $response = (new AuthMiddleware())->handle();

        $this->assertSame('', $response);
    }

    public function testCsrfMiddlewareRejectsInvalidPostToken(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REQUEST_URI'] = '/contact/submit';
        $_SESSION['csrf_token'] = 'known-token';
        $_POST['_token'] = 'bad-token';

        $this->expectException(TokenMismatchException::class);

        (new VerifyCsrfToken())->handle();
    }

    public function testRateLimitMiddlewareThrowsAfterConfiguredAttempts(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REQUEST_URI'] = '/api/contact';
        $_SERVER['REMOTE_ADDR'] = '127.0.0.' . random_int(200, 250);

        $middleware = new RateLimitMiddleware();
        $middleware->setParameters(['1', '60']);
        $middleware->handle();

        $this->expectException(ThrottleRequestsException::class);

        $middleware->handle();
    }
}
