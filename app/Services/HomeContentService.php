<?php

namespace App\Services;

use App\Helpers\SeoHelper;
use App\Repositories\PageRepository;
use App\Repositories\PageSectionRepository;
use App\Repositories\ProjectRepository;
use App\Repositories\ServiceRepository;
use App\Repositories\SiteSettingRepository;

/**
 * Assembles CMS-managed content required by the public home page.
 */
class HomeContentService
{
    public function __construct(
        private PageRepository $pages,
        private PageSectionRepository $sections,
        private ServiceRepository $services,
        private ProjectRepository $projects,
        private SiteSettingRepository $settings
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function getContent(): array
    {
        $page = $this->pages->findPublishedBySlug('home');
        if ($page === null) {
            throw new \RuntimeException('The published home page content is missing. Run the database seeder or publish a page with slug "home".');
        }

        $sections = $this->indexSections($this->sections->forPage((int) $page['id']));
        $settings = $this->settings->publicSettings();
        $seo = $this->seoFromPage($page, $settings);

        return [
            'page' => $page,
            'title' => (string) ($page['meta_title'] ?: $page['title']),
            'hero' => $sections['hero'] ?? [],
            'servicesIntro' => $sections['services_intro'] ?? [],
            'whyChooseUs' => $sections['why_choose_us'] ?? [],
            'hseCommitment' => $sections['hse_commitment'] ?? [],
            'projectsIntro' => $sections['projects_intro'] ?? [],
            'clientsSection' => $sections['clients'] ?? [],
            'cta' => $sections['cta'] ?? [],
            'services' => $this->publishedServices(),
            'projects' => array_slice($this->publishedProjects(), 0, 3),
            'seo' => $seo,
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
        return array_map(function (array $service): array {
            return [
                'slug' => (string) $service['slug'],
                'title' => (string) $service['title'],
                'icon' => (string) ($service['icon'] ?? 'SR'),
                'summary' => (string) ($service['summary'] ?? ''),
            ];
        }, $this->services->published());
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function publishedProjects(): array
    {
        return array_map(function (array $project): array {
            return [
                'slug' => (string) ($project['slug'] ?? ''),
                'title' => (string) ($project['title'] ?? ''),
                'summary' => (string) ($project['summary'] ?? ''),
                'category' => (string) ($project['category'] ?? ''),
                'image' => (string) ($project['featured_image'] ?? ''),
            ];
        }, $this->projects->published());
    }

    /**
     * @param array<string, mixed> $page
     * @param array<string, mixed> $settings
     * @return array<string, mixed>
     */
    private function seoFromPage(array $page, array $settings): array
    {
        return [
            'title' => (string) ($page['meta_title'] ?: $page['title']),
            'description' => (string) ($page['meta_description'] ?: $page['excerpt'] ?: ''),
            'keywords' => (string) ($page['meta_keywords'] ?: ''),
            'canonical' => (string) ($settings['site.url'] ?? 'https://www.desnkygroup.com/'),
            'image' => (string) ($page['featured_image'] ?? ''),
            'schema' => [
                SeoHelper::organizationSchema(),
                SeoHelper::localBusinessSchema(),
                SeoHelper::websiteSchema(),
            ],
        ];
    }
}
