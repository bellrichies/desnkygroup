<?php

namespace App\Services;

use App\Helpers\SeoHelper;
use App\Repositories\PageRepository;
use App\Repositories\PageSectionRepository;
use App\Repositories\ServiceRepository;
use App\Repositories\SiteSettingRepository;

/**
 * Assembles CMS-managed content required by the public about page.
 */
class AboutContentService
{
    public function __construct(
        private PageRepository $pages,
        private PageSectionRepository $sections,
        private ServiceRepository $services,
        private SiteSettingRepository $settings
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function getContent(): array
    {
        $page = $this->pages->findPublishedBySlug('about');
        if ($page === null) {
            throw new \RuntimeException('The published about page content is missing. Run the database seeder or publish a page with slug "about".');
        }

        $sections = $this->indexSections($this->sections->forPage((int) $page['id']));

        return [
            'title' => (string) ($page['meta_title'] ?: $page['title']),
            'page' => $page,
            'hero' => $sections['hero'] ?? [],
            'overview' => $sections['overview'] ?? [],
            'missionVision' => $sections['mission_vision'] ?? [],
            'sectors' => $sections['sectors'] ?? [],
            'operatingModel' => $sections['operating_model'] ?? [],
            'values' => $sections['values'] ?? [],
            'stats' => $sections['stats'] ?? [],
            'team' => $sections['team'] ?? [],
            'cta' => $sections['cta'] ?? [],
            'services' => $this->publishedServices(),
            'seo' => $this->pageSeo($page),
        ];
    }

    /**
     * @param array<int, array<string, mixed>> $rows
     * @return array<string, array<string, mixed>>
     */
    private function indexSections(array $rows): array
    {
        $sections = [];

        foreach ($rows as $row) {
            $body = $this->decodeBody((string) ($row['body'] ?? ''));
            $sections[(string) $row['section_key']] = array_merge($body, [
                'key' => (string) $row['section_key'],
                'heading' => (string) ($row['heading'] ?? ''),
            ]);
        }

        return $sections;
    }

    /**
     * @return array<string, mixed>
     */
    private function decodeBody(string $body): array
    {
        if ($body === '') {
            return [];
        }

        $decoded = json_decode($body, true);
        if (is_array($decoded)) {
            return $decoded;
        }

        return ['text' => $body];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function publishedServices(): array
    {
        return array_map(static fn (array $row): array => [
            'slug' => (string) ($row['slug'] ?? ''),
            'title' => (string) ($row['title'] ?? ''),
            'summary' => (string) ($row['summary'] ?? ''),
            'image' => (string) ($row['featured_image'] ?? ''),
        ], $this->services->published());
    }

    /**
     * @param array<string, mixed> $page
     * @return array<string, mixed>
     */
    private function pageSeo(array $page): array
    {
        $settings = $this->settings->publicSettings();
        $baseUrl = rtrim((string) ($settings['site.url'] ?? 'https://www.desnkygroup.com'), '/');

        return [
            'title' => (string) ($page['meta_title'] ?: $page['title']),
            'description' => (string) ($page['meta_description'] ?: $page['excerpt'] ?: ''),
            'keywords' => (string) ($page['meta_keywords'] ?: ''),
            'canonical' => $baseUrl . '/about',
            'image' => (string) ($page['featured_image'] ?? ''),
            'schema' => [
                SeoHelper::organizationSchema(),
                SeoHelper::localBusinessSchema(),
                SeoHelper::breadcrumbSchema([
                    'Home' => $baseUrl . '/',
                    'About' => $baseUrl . '/about',
                ]),
            ],
        ];
    }
}
