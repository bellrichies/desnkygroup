<?php

namespace App\Services;

use App\Config;
use App\Controllers\Frontend\ServiceController;
use App\Controllers\Frontend\ShopController;
use App\Repositories\ProductRepository;
use App\Repositories\ProjectRepository;
use App\Repositories\ServiceRepository;
use App\Support\DatabaseFactory;
use Throwable;

/**
 * Builds XML sitemap entries from static routes and published content.
 */
class SitemapService extends BaseService
{
    /**
     * @return array<int, array<string, string>>
     */
    public function entries(): array
    {
        $entries = [
            $this->entry('/', '1.0', 'weekly'),
            $this->entry('/about', '0.8', 'monthly'),
            $this->entry('/services', '0.9', 'weekly'),
            $this->entry('/projects', '0.8', 'weekly'),
            $this->entry('/hse-policy', '0.7', 'monthly'),
            $this->entry('/contact', '0.8', 'monthly'),
            $this->entry('/shop', '0.8', 'weekly'),
        ];

        foreach ($this->serviceSlugs() as $slug) {
            $entries[] = $this->entry('/services/' . $slug, '0.85', 'monthly');
        }

        foreach ($this->productSlugs() as $slug) {
            $entries[] = $this->entry('/shop/product/' . $slug, '0.7', 'weekly');
        }

        foreach ($this->categorySlugs() as $slug) {
            $entries[] = $this->entry('/shop/category/' . $slug, '0.65', 'weekly');
        }

        foreach ($this->projectSlugs() as $slug) {
            $entries[] = $this->entry('/projects/' . $slug, '0.65', 'monthly');
        }

        return $this->uniqueEntries($entries);
    }

    public function xml(): string
    {
        $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        $xml .= "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";

        foreach ($this->entries() as $entry) {
            $xml .= "    <url>\n";
            $xml .= '        <loc>' . htmlspecialchars($entry['loc'], ENT_XML1, 'UTF-8') . "</loc>\n";
            $xml .= '        <lastmod>' . $entry['lastmod'] . "</lastmod>\n";
            $xml .= '        <changefreq>' . $entry['changefreq'] . "</changefreq>\n";
            $xml .= '        <priority>' . $entry['priority'] . "</priority>\n";
            $xml .= "    </url>\n";
        }

        return $xml . "</urlset>\n";
    }

    public function robots(): string
    {
        $baseUrl = $this->baseUrl();

        return implode("\n", [
            'User-agent: *',
            'Allow: /',
            'Disallow: /admin',
            'Disallow: /storage',
            'Disallow: /vendor',
            'Disallow: /database',
            'Disallow: /config',
            'Disallow: /scripts',
            'Crawl-delay: 5',
            '',
            'Sitemap: ' . $baseUrl . '/sitemap.xml',
            '',
        ]);
    }

    public function writePublicFiles(?string $publicPath = null): void
    {
        $publicPath ??= base_path('public');
        $xml = $this->xml();

        file_put_contents($publicPath . '/sitemap.xml', $xml);
        file_put_contents($publicPath . '/sitemap.xml.gz', gzencode($xml, 9));
        file_put_contents($publicPath . '/robots.txt', $this->robots());
    }

    private function entry(string $path, string $priority, string $changefreq): array
    {
        return [
            'loc' => $this->baseUrl() . ($path === '/' ? '/' : $path),
            'lastmod' => date('Y-m-d'),
            'changefreq' => $changefreq,
            'priority' => $priority,
        ];
    }

    /**
     * @return array<int, string>
     */
    private function serviceSlugs(): array
    {
        try {
            $rows = (new ServiceRepository(DatabaseFactory::make()))->published();

            if ($rows !== []) {
                return array_map(static fn (array $row): string => (string) $row['slug'], $rows);
            }
        } catch (Throwable) {
        }

        return array_keys(ServiceController::services());
    }

    /**
     * @return array<int, string>
     */
    private function productSlugs(): array
    {
        return array_keys(ShopController::products());
    }

    /**
     * @return array<int, string>
     */
    private function categorySlugs(): array
    {
        try {
            $rows = (new ProductRepository(DatabaseFactory::make()))->activeCategories();

            if ($rows !== []) {
                return array_map(static fn (array $row): string => (string) $row['slug'], $rows);
            }
        } catch (Throwable) {
        }

        return array_keys(ShopController::categories());
    }

    /**
     * @return array<int, string>
     */
    private function projectSlugs(): array
    {
        try {
            $rows = (new ProjectRepository(DatabaseFactory::make()))->published();

            return array_values(array_filter(array_map(
                static fn (array $row): string => (string) ($row['slug'] ?? ''),
                $rows
            )));
        } catch (Throwable) {
            return [];
        }
    }

    /**
     * @param array<int, array<string, string>> $entries
     * @return array<int, array<string, string>>
     */
    private function uniqueEntries(array $entries): array
    {
        $unique = [];

        foreach ($entries as $entry) {
            $unique[$entry['loc']] = $entry;
        }

        return array_values($unique);
    }

    private function baseUrl(): string
    {
        return rtrim((string) Config::get('seo.base_url', 'https://www.desnkygroup.com'), '/');
    }
}
