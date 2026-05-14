<?php

namespace Tests\Unit;

use App\Services\CacheService;
use PHPUnit\Framework\TestCase;

class CacheServiceTest extends TestCase
{
    private string $directory;

    protected function setUp(): void
    {
        $this->directory = sys_get_temp_dir() . '/desnky-cache-test-' . bin2hex(random_bytes(4));
    }

    protected function tearDown(): void
    {
        foreach (glob($this->directory . '/*.json') ?: [] as $path) {
            @unlink($path);
        }

        @rmdir($this->directory);
    }

    public function testRememberCachesCallbackValue(): void
    {
        $cache = new CacheService($this->directory);
        $calls = 0;

        $first = $cache->remember('phase-eight', 60, function () use (&$calls): array {
            $calls++;
            return ['status' => 'cached'];
        }, ['test']);

        $second = $cache->remember('phase-eight', 60, function () use (&$calls): array {
            $calls++;
            return ['status' => 'miss'];
        }, ['test']);

        $this->assertSame(['status' => 'cached'], $first);
        $this->assertSame(['status' => 'cached'], $second);
        $this->assertSame(1, $calls);
    }

    public function testFlushTagRemovesTaggedEntries(): void
    {
        $cache = new CacheService($this->directory);
        $cache->put('services.published', ['Engineering'], 60, ['services']);

        $cache->flushTag('services');

        $this->assertSame('missing', $cache->get('services.published', 'missing'));
    }
}
