<?php

namespace App\Services;

use App\Helpers\SeoHelper;
use App\Repositories\PageRepository;
use App\Repositories\PageSectionRepository;
use App\Repositories\ServiceRepository;
use App\Repositories\SiteSettingRepository;

/**
 * Assembles CMS-managed content required by public service pages.
 */
class ServicePageContentService
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
    public function indexContent(): array
    {
        $page = $this->publishedServicesPage();
        $sections = $this->indexSections($this->sections->forPage((int) $page['id']));

        return [
            'title' => (string) ($page['meta_title'] ?: $page['title']),
            'page' => $page,
            'hero' => $sections['hero'] ?? [],
            'listing' => $sections['listing'] ?? [],
            'services' => $this->publishedServices(),
            'seo' => $this->pageSeo($page),
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    public function showContent(string $slug): ?array
    {
        $page = $this->publishedServicesPage();
        $sections = $this->indexSections($this->sections->forPage((int) $page['id']));
        $service = $this->services->findPublishedBySlug($slug);

        if ($service === null) {
            return null;
        }

        $service = $this->normalizeService($service);
        $related = array_values(array_filter(
            $this->publishedServices(),
            static fn (array $item): bool => $item['slug'] !== $slug
        ));

        return [
            'title' => $service['title'],
            'page' => $page,
            'service' => $service,
            'detail' => $sections['detail'] ?? [],
            'relatedServices' => $related,
            'seo' => $this->serviceSeo($service, $slug),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function publishedServicesPage(): array
    {
        $page = $this->pages->findPublishedBySlug('services');
        if ($page === null) {
            throw new \RuntimeException('The published services page content is missing. Run the database seeder or publish a page with slug "services".');
        }

        return $page;
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
        return array_map(fn (array $row): array => $this->normalizeService($row), $this->services->published());
    }

    /**
     * @param array<string, mixed> $row
     * @return array<string, mixed>
     */
    private function normalizeService(array $row): array
    {
        $structured = $this->decodeBody((string) ($row['content'] ?? ''));

        return [
            'slug' => (string) $row['slug'],
            'title' => (string) $row['title'],
            'icon' => (string) ($row['icon'] ?? 'SR'),
            'category' => (string) ($row['category'] ?? ''),
            'summary' => (string) ($row['summary'] ?? ''),
            'image' => (string) ($row['featured_image'] ?? ''),
            'overview' => (string) ($structured['overview'] ?? $row['content'] ?? ''),
            'features' => $this->stringList($structured['features'] ?? []),
            'process' => $this->stringList($structured['process'] ?? []),
            'benefits' => $this->stringList($structured['benefits'] ?? []),
            'seo_title' => (string) ($row['meta_title'] ?? $row['title']),
            'seo_description' => (string) ($row['meta_description'] ?? $row['summary'] ?? ''),
            'seo_keywords' => (string) ($row['meta_keywords'] ?? ''),
            'canonical_url' => (string) ($row['canonical_url'] ?? ''),
            'og_image' => (string) ($row['og_image'] ?? ''),
        ];
    }

    /**
     * @param mixed $value
     * @return array<int, string>
     */
    private function stringList(mixed $value): array
    {
        if (!is_array($value)) {
            return [];
        }

        return array_values(array_filter(array_map(
            static fn (mixed $item): string => trim((string) $item),
            $value
        )));
    }

    /**
     * @param array<string, mixed> $page
     * @return array<string, mixed>
     */
    private function pageSeo(array $page): array
    {
        $settings = $this->settings->publicSettings();

        return [
            'title' => (string) ($page['meta_title'] ?: $page['title']),
            'description' => (string) ($page['meta_description'] ?: $page['excerpt'] ?: ''),
            'keywords' => (string) ($page['meta_keywords'] ?: ''),
            'canonical' => rtrim((string) ($settings['site.url'] ?? 'https://www.desnkygroup.com'), '/') . '/services',
            'image' => (string) ($page['featured_image'] ?? ''),
            'schema' => [
                SeoHelper::organizationSchema(),
                SeoHelper::breadcrumbSchema([
                    'Home' => rtrim((string) ($settings['site.url'] ?? 'https://www.desnkygroup.com'), '/') . '/',
                    'Services' => rtrim((string) ($settings['site.url'] ?? 'https://www.desnkygroup.com'), '/') . '/services',
                ]),
            ],
        ];
    }

    /**
     * @param array<string, mixed> $service
     * @return array<string, mixed>
     */
    private function serviceSeo(array $service, string $slug): array
    {
        $settings = $this->settings->publicSettings();
        $baseUrl = rtrim((string) ($settings['site.url'] ?? 'https://www.desnkygroup.com'), '/');

        return [
            'title' => $service['seo_title'],
            'description' => $service['seo_description'],
            'keywords' => $service['seo_keywords'] ?: $service['title'] . ', services Nigeria, Desnky Global',
            'canonical' => $service['canonical_url'] ?: $baseUrl . '/services/' . $slug,
            'image' => $service['og_image'] ?: $service['image'],
            'schema' => [
                SeoHelper::serviceSchema($service),
                SeoHelper::breadcrumbSchema([
                    'Home' => $baseUrl . '/',
                    'Services' => $baseUrl . '/services',
                    $service['title'] => $baseUrl . '/services/' . $slug,
                ]),
                SeoHelper::faqSchema([
                    'Does Desnky Global provide ' . $service['title'] . ' in Nigeria?' => 'Yes. Desnky Global Resources Ltd supports Nigerian organizations with ' . strtolower((string) $service['title']) . ' and related business services.',
                    'How can I request this service?' => 'Use the contact form or request a quote so the team can review your requirement and respond with next steps.',
                ]),
            ],
        ];
    }
}
