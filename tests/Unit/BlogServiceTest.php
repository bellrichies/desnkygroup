<?php

namespace Tests\Unit;

use App\Repositories\BlogAdRepository;
use App\Repositories\BlogPostRepository;
use App\Repositories\BlogTaxonomyRepository;
use App\Services\BlogContentSanitizer;
use App\Services\BlogService;
use InvalidArgumentException;
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

    public function testBulkDeleteNormalizesIdsAndReturnsAffectedCount(): void
    {
        $posts = new class extends BlogPostRepository {
            /** @var array<int, int> */
            public array $receivedIds = [];

            public function __construct()
            {
            }

            public function softDeleteMany(array $ids): int
            {
                $this->receivedIds = $ids;

                return count($ids);
            }
        };

        $service = $this->service($posts);

        $this->assertSame(2, $service->bulkDeletePosts(['4', 4, 0, -1, '9']));
        $this->assertSame([4, 9], $posts->receivedIds);
    }

    public function testBulkDeleteRequiresASelection(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Select at least one post');

        $this->service()->bulkDeletePosts([]);
    }

    private function service(?BlogPostRepository $posts = null): BlogService
    {
        $posts ??= new class extends BlogPostRepository {
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
