<?php

namespace App;

use App\Exceptions\ApplicationException;
use App\Exceptions\AuthorizationException;
use App\Exceptions\NotFoundException;
use App\Exceptions\TokenMismatchException;
use App\Exceptions\ValidationException;
use Throwable;

/**
 * Converts exceptions into logged HTTP responses.
 */
class ExceptionHandler
{
    private Logger $logger;

    public function __construct(?Logger $logger = null)
    {
        $this->logger = $logger ?: new Logger();
    }

    /**
     * Render an exception response.
     *
     * @param Throwable $exception Throwable to render.
     * @return void
     */
    public function render(Throwable $exception): void
    {
        $this->logger->exception($exception);

        $status = $this->statusCode($exception);
        http_response_code($status);

        if ($this->expectsJson()) {
            header('Content-Type: application/json');
            echo json_encode($this->jsonPayload($exception, $status));
            return;
        }

        header('Content-Type: text/html; charset=UTF-8');
        echo $this->htmlPayload($exception, $status);
    }

    /**
     * Register native PHP error handling.
     *
     * @return void
     */
    public function register(): void
    {
        set_error_handler(function (int $severity, string $message, string $file, int $line): bool {
            if (!(error_reporting() & $severity)) {
                return false;
            }

            throw new \ErrorException($message, 0, $severity, $file, $line);
        });
    }

    private function statusCode(Throwable $exception): int
    {
        if ($exception instanceof ValidationException) {
            return 422;
        }

        if ($exception instanceof TokenMismatchException) {
            return 419;
        }

        if ($exception instanceof AuthorizationException) {
            return 403;
        }

        if ($exception instanceof NotFoundException) {
            return 404;
        }

        if ($exception instanceof ApplicationException && $exception->getCode() >= 400) {
            return (int) $exception->getCode();
        }

        return 500;
    }

    /**
     * @return array<string, mixed>
     */
    private function jsonPayload(Throwable $exception, int $status): array
    {
        $payload = [
            'message' => $this->publicMessage($exception, $status),
            'status' => $status,
        ];

        if ($exception instanceof ValidationException) {
            $payload['errors'] = $exception->getErrors();
        }

        if ((bool) Config::get('app.debug', false)) {
            $payload['debug'] = [
                'exception' => $exception::class,
                'detail' => $exception->getMessage(),
            ];
        }

        return $payload;
    }

    private function htmlPayload(Throwable $exception, int $status): string
    {
        if ((bool) Config::get('app.debug', false)) {
            $message = htmlspecialchars($this->publicMessage($exception, $status), ENT_QUOTES, 'UTF-8');
            $title = htmlspecialchars((string) $status, ENT_QUOTES, 'UTF-8');
            $detail = htmlspecialchars($exception->getMessage(), ENT_QUOTES, 'UTF-8');
            return "<h1>{$title}</h1><p>{$message}</p><pre>{$detail}</pre>";
        }

        return (new View())->setLayout('frontend/layouts/app')->render('errors/error', [
            'status' => $status,
            'message' => $this->publicMessage($exception, $status),
            'title' => $status === 404 ? 'Page Not Found' : 'Website Error',
            'seo' => [
                'title' => $status === 404 ? 'Page Not Found | Desnky Global' : 'Website Error | Desnky Global',
                'description' => 'The requested Desnky Global Resources page could not be loaded.',
            ],
        ]);
    }

    private function publicMessage(Throwable $exception, int $status): string
    {
        if ($status < 500) {
            return $exception->getMessage();
        }

        return 'Internal Server Error';
    }

    private function expectsJson(): bool
    {
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
        $requestedWith = $_SERVER['HTTP_X_REQUESTED_WITH'] ?? '';

        return str_contains($accept, 'application/json')
            || strtolower($requestedWith) === 'xmlhttprequest'
            || str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/api/');
    }
}
