<?php

namespace Tests\Unit;

use App\Services\SitemapService;
use PHPUnit\Framework\TestCase;

class SitemapServiceTest extends TestCase
{
    public function testXmlIncludesCoreRoutesAndAbsoluteUrls(): void
    {
        $xml = (new SitemapService())->xml();

        $this->assertStringContainsString('<urlset', $xml);
        $this->assertStringContainsString('https://www.desnkygroup.com/services', $xml);
        $this->assertStringContainsString('https://www.desnkygroup.com/contact', $xml);
        $this->assertStringContainsString('<lastmod>', $xml);
    }

    public function testRobotsDisallowsPrivatePathsAndReferencesSitemap(): void
    {
        $robots = (new SitemapService())->robots();

        $this->assertStringContainsString('Disallow: /admin', $robots);
        $this->assertStringContainsString('Disallow: /storage', $robots);
        $this->assertStringContainsString('Sitemap: https://www.desnkygroup.com/sitemap.xml', $robots);
    }
}
