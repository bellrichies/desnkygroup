<?php

namespace App\Controllers\Frontend;

use App\Controllers\BaseController;
use App\Services\SitemapService;

/**
 * Serves SEO discovery assets when the web server routes them through PHP.
 */
class SeoAssetController extends BaseController
{
    public function sitemap(): string
    {
        header('Content-Type: application/xml; charset=UTF-8');

        return (new SitemapService())->xml();
    }

    public function sitemapGzip(): string
    {
        header('Content-Type: application/x-gzip');
        header('Content-Disposition: inline; filename="sitemap.xml.gz"');

        return (string) gzencode((new SitemapService())->xml(), 9);
    }

    public function robots(): string
    {
        header('Content-Type: text/plain; charset=UTF-8');

        return (new SitemapService())->robots();
    }

    public function ads(): string
    {
        header('Content-Type: text/plain; charset=UTF-8');

        $path = dirname(__DIR__, 3) . '/public/ads.txt';

        return is_file($path) ? (string) file_get_contents($path) : '';
    }
}
