<?php

namespace Tests\Unit;

use App\Repositories\BlogAdRepository;
use App\Repositories\BlogPostRepository;
use App\Repositories\BlogTaxonomyRepository;
use App\Services\BlogContentSanitizer;
use App\Services\BlogService;
use PHPUnit\Framework\TestCase;

class BlogServiceTest extends TestCase
{
    public function testReadingTimeUsesAtLeastOneMinute(): void
    {
        $service = $this->service();

        $this->assertSame(1, $service->readingTime('<p>Short post.</p>'));
    }

    public function testReadingTimeRoundsUpAtTwoHundredWords(): void
    {
        $service = $this->service();
        $content = '<p>' . str_repeat('word ', 201) . '</p>';

        $this->assertSame(2, $service->readingTime($content));
    }

    private function service(): BlogService
    {
        $posts = new class extends BlogPostRepository {
            public function __construct()
            {
            }
        };

        $taxonomy = new class extends BlogTaxonomyRepository {
            public function __construct()
            {
            }
        };

        $ads = new class extends BlogAdRepository {
            public function __construct()
            {
            }
        };

        return new BlogService($posts, $taxonomy, $ads, new BlogContentSanitizer(), null);
    }
}
