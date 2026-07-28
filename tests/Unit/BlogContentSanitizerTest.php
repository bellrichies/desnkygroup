<?php

namespace Tests\Unit;

use App\Services\BlogContentSanitizer;
use PHPUnit\Framework\TestCase;

class BlogContentSanitizerTest extends TestCase
{
    public function testItRemovesScriptsEventHandlersAndJavascriptUrls(): void
    {
        $sanitizer = new BlogContentSanitizer();

        $html = '<h2 onclick="alert(1)">Heading</h2><p>Safe <a href="javascript:alert(1)">link</a></p><script>alert(1)</script>';
        $clean = $sanitizer->sanitize($html);

        $this->assertStringContainsString('<h2>Heading</h2>', $clean);
        $this->assertStringContainsString('<p>Safe <a>link</a></p>', $clean);
        $this->assertStringNotContainsString('onclick', $clean);
        $this->assertStringNotContainsString('javascript:', $clean);
        $this->assertStringNotContainsString('<script', $clean);
    }

    public function testItKeepsApprovedEditorialMarkup(): void
    {
        $sanitizer = new BlogContentSanitizer();

        $html = '<blockquote cite="https://example.com">Quote</blockquote><ul><li>Item</li></ul><pre><code>echo 1;</code></pre>';
        $clean = $sanitizer->sanitize($html);

        $this->assertStringContainsString('<blockquote cite="https://example.com">Quote</blockquote>', $clean);
        $this->assertStringContainsString('<ul><li>Item</li></ul>', $clean);
        $this->assertStringContainsString('<pre><code>echo 1;</code></pre>', $clean);
    }
}
