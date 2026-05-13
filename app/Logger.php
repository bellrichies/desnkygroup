<?php

namespace App;

use Throwable;

/**
 * Minimal file logger for framework-level events.
 */
class Logger
{
    private string $logPath;

    /**
     * @param string|null $logPath Absolute log file path.
     */
    public function __construct(?string $logPath = null)
    {
        $basePath = defined('STORAGE_PATH') ? STORAGE_PATH : dirname(__DIR__) . '/storage';
        $this->logPath = $logPath ?: $basePath . '/logs/app.log';
    }

    /**
     * Log an informational message.
     *
     * @param string $message Log message.
     * @param array<string, mixed> $context Structured context.
     * @return void
     */
    public function info(string $message, array $context = []): void
    {
        $this->write('INFO', $message, $context);
    }

    /**
     * Log an error message.
     *
     * @param string $message Log message.
     * @param array<string, mixed> $context Structured context.
     * @return void
     */
    public function error(string $message, array $context = []): void
    {
        $this->write('ERROR', $message, $context);
    }

    /**
     * Log an exception with trace metadata.
     *
     * @param Throwable $exception Exception to log.
     * @return void
     */
    public function exception(Throwable $exception): void
    {
        $this->error($exception->getMessage(), [
            'class' => $exception::class,
            'code' => $exception->getCode(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }

    /**
     * Persist a log entry.
     *
     * @param string $level Severity level.
     * @param string $message Log message.
     * @param array<string, mixed> $context Structured context.
     * @return void
     */
    private function write(string $level, string $message, array $context = []): void
    {
        $directory = dirname($this->logPath);
        if (!is_dir($directory)) {
            mkdir($directory, 0775, true);
        }

        $payload = [
            'time' => date('c'),
            'level' => $level,
            'message' => $message,
            'context' => $context,
        ];

        file_put_contents(
            $this->logPath,
            json_encode($payload, JSON_UNESCAPED_SLASHES) . PHP_EOL,
            FILE_APPEND | LOCK_EX
        );
    }
}
