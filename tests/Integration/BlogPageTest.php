<?php
use PHPUnit\Framework\TestCase;

final class BlogPageTest extends TestCase
{
    public function testBlogListingReturnsHtmlWithArticles(): void
    {
        $url = 'http://127.0.0.1:8000/blog';
        $opts = [
            'http' => [
                'method' => 'GET',
                'header' => "User-Agent: PHPUnitTest/1.0\r\n",
                'timeout' => 10,
            ],
        ];
        $context = stream_context_create($opts);
        $html = @file_get_contents($url, false, $context);

        $this->assertNotFalse($html, 'Failed to fetch the blog page. Ensure local dev server is running.');

        // Assert the listing contains at least one article element or post link
        $hasArticle = stripos($html, '<article') !== false;
        $hasPostLink = stripos($html, '/blog/') !== false;

        $this->assertTrue($hasArticle || $hasPostLink, 'Blog listing page does not appear to contain posts.');
    }
}
