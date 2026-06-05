<?php

namespace App\Services;

use App\Helpers\SeoHelper;
use App\Repositories\PageRepository;
use App\Repositories\PageSectionRepository;
use App\Repositories\SiteSettingRepository;

/**
 * Assembles CMS-managed content required by the public HSE policy page.
 */
class HseContentService
{
    public function __construct(
        private PageRepository $pages,
        private PageSectionRepository $sections,
        private SiteSettingRepository $settings
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function getContent(): array
    {
        $page = $this->pages->findPublishedBySlug('hse-policy');
        if ($page === null) {
            throw new \RuntimeException('The published HSE policy page content is missing. Run the database seeder or publish a page with slug "hse-policy".');
        }

        $sections = $this->indexSections($this->sections->forPage((int) $page['id']));

        return [
            'title' => (string) ($page['meta_title'] ?: $page['title']),
            'page' => $page,
            'hero' => $sections['hero'] ?? [],
            'policyStatement' => $sections['policy_statement'] ?? [],
            'objective' => $sections['objective'] ?? [],
            'managementCommitments' => $sections['management_commitments'] ?? [],
            'communication' => $sections['communication'] ?? [],
            'culture' => $sections['culture'] ?? [],
            'cta' => $sections['cta'] ?? [],
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
            'canonical' => $baseUrl . '/hse-policy',
            'image' => (string) ($page['featured_image'] ?? ''),
            'schema' => [
                SeoHelper::organizationSchema(),
                SeoHelper::breadcrumbSchema([
                    'Home' => $baseUrl . '/',
                    'HSE Policy' => $baseUrl . '/hse-policy',
                ]),
            ],
        ];
    }
}
