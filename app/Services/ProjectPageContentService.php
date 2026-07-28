<?php

namespace App\Services;

use App\Helpers\SeoHelper;
use App\Repositories\PageRepository;
use App\Repositories\PageSectionRepository;
use App\Repositories\ProjectRepository;
use App\Repositories\SiteSettingRepository;

/**
 * Assembles CMS-managed content required by public project pages.
 */
class ProjectPageContentService
{
    public function __construct(
        private PageRepository $pages,
        private PageSectionRepository $sections,
        private ProjectRepository $projects,
        private SiteSettingRepository $settings
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function indexContent(): array
    {
        $page = $this->publishedProjectsPage();
        $sections = $this->indexSections($this->sections->forPage((int) $page['id']));
        $projects = $this->publishedProjects();

        return [
            'title' => (string) ($page['meta_title'] ?: $page['title']),
            'page' => $page,
            'hero' => $sections['hero'] ?? [],
            'listing' => $sections['listing'] ?? [],
            'projects' => $projects,
            'categories' => $this->categories($projects),
            'seo' => $this->pageSeo($page),
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    public function showContent(string $slug): ?array
    {
        $page = $this->publishedProjectsPage();
        $sections = $this->indexSections($this->sections->forPage((int) $page['id']));
        $project = $this->projects->findPublishedBySlug($slug);

        if ($project === null) {
            return null;
        }

        $projectId = (int) ($project['id'] ?? 0);
        $project = $this->normalizeProject($project);
        $project['gallery'] = $this->normalizeGallery(
            $this->projects->galleryImages($projectId)
        );

        return [
            'title' => $project['title'],
            'page' => $page,
            'project' => $project,
            'detail' => $sections['detail'] ?? [],
            'seo' => $this->projectSeo($project, $slug),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function publishedProjectsPage(): array
    {
        $page = $this->pages->findPublishedBySlug('projects');
        if ($page === null) {
            throw new \RuntimeException('The published projects page content is missing. Run the database seeder or publish a page with slug "projects".');
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
        $body = trim($body);
        if ($body === '') {
            return [];
        }

        $decoded = json_decode($body, true);
        if (is_array($decoded)) {
            return $decoded;
        }

        // WYSIWYG editors may wrap a structured JSON payload in paragraph tags
        // or encode its quotation marks. Normalize that representation before
        // falling back to ordinary prose.
        $plainBody = html_entity_decode(
            strip_tags(
                (string) preg_replace(
                    '/<(?:br)\s*\/?>|<\/(?:p|div|li|h[1-6])>/i',
                    "\n",
                    $body
                )
            ),
            ENT_QUOTES | ENT_HTML5,
            'UTF-8'
        );
        $plainBody = trim($plainBody, "\xEF\xBB\xBF \t\n\r\0\x0B");

        $decoded = json_decode($plainBody, true);
        if (is_array($decoded)) {
            return $decoded;
        }

        return ['overview' => preg_replace("/\n{3,}/", "\n\n", $plainBody) ?? $plainBody];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function publishedProjects(): array
    {
        return array_map(fn (array $row): array => $this->normalizeProject($row), $this->projects->published());
    }

    /**
     * @param array<string, mixed> $row
     * @return array<string, mixed>
     */
    private function normalizeProject(array $row): array
    {
        $structured = $this->decodeBody((string) ($row['description'] ?? ''));

        return [
            'slug' => (string) ($row['slug'] ?? ''),
            'title' => (string) ($row['title'] ?? ''),
            'summary' => (string) ($row['summary'] ?? ''),
            'overview' => (string) ($structured['overview'] ?? $row['description'] ?? ''),
            'category' => (string) ($row['category'] ?? 'Project'),
            'client_name' => (string) ($row['client_name'] ?? ''),
            'project_date' => (string) ($row['project_date'] ?? ''),
            'image' => (string) ($row['featured_image'] ?? ''),
            'highlights' => $this->stringList($structured['highlights'] ?? []),
            'outcomes' => $this->stringList($structured['outcomes'] ?? []),
            'meta_title' => (string) ($row['meta_title'] ?? ''),
            'meta_description' => (string) ($row['meta_description'] ?? ''),
            'meta_keywords' => (string) ($row['meta_keywords'] ?? ''),
            'canonical_url' => (string) ($row['canonical_url'] ?? ''),
            'og_image' => (string) ($row['og_image'] ?? ''),
        ];
    }

    /**
     * @param array<int, array<string, mixed>> $rows
     * @return array<int, array{path: string, alt_text: string}>
     */
    private function normalizeGallery(array $rows): array
    {
        $images = [];

        foreach ($rows as $row) {
            $path = trim((string) ($row['path'] ?? ''));
            if ($path === '') {
                continue;
            }

            $images[] = [
                'path' => $path,
                'alt_text' => trim((string) ($row['alt_text'] ?? '')),
            ];
        }

        return $images;
    }

    /**
     * @param array<int, array<string, mixed>> $projects
     * @return array<int, string>
     */
    private function categories(array $projects): array
    {
        $categories = [];
        foreach ($projects as $project) {
            $category = trim((string) ($project['category'] ?? ''));
            if ($category !== '') {
                $categories[$category] = $category;
            }
        }

        return array_values($categories);
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
        $baseUrl = rtrim((string) ($settings['site.url'] ?? 'https://www.desnkygroup.com'), '/');

        return [
            'title' => (string) ($page['meta_title'] ?: $page['title']),
            'description' => (string) ($page['meta_description'] ?: $page['excerpt'] ?: ''),
            'keywords' => (string) ($page['meta_keywords'] ?: ''),
            'canonical' => $baseUrl . '/projects',
            'image' => (string) ($page['featured_image'] ?? ''),
            'schema' => [
                SeoHelper::organizationSchema(),
                SeoHelper::breadcrumbSchema([
                    'Home' => $baseUrl . '/',
                    'Projects' => $baseUrl . '/projects',
                ]),
            ],
        ];
    }

    /**
     * @param array<string, mixed> $project
     * @return array<string, mixed>
     */
    private function projectSeo(array $project, string $slug): array
    {
        $settings = $this->settings->publicSettings();
        $baseUrl = rtrim((string) ($settings['site.url'] ?? 'https://www.desnkygroup.com'), '/');

        return [
            'title' => $project['meta_title'] ?: $project['title'] . ' | Desnky Projects',
            'description' => $project['meta_description'] ?: $project['summary'],
            'keywords' => $project['meta_keywords'] ?: $project['title'] . ', Desnky project, Nigeria',
            'canonical' => $project['canonical_url'] ?: $baseUrl . '/projects/' . $slug,
            'image' => $project['og_image'] ?: $project['image'],
            'schema' => [
                [
                    '@context' => 'https://schema.org',
                    '@type' => 'CreativeWork',
                    'name' => $project['title'],
                    'description' => $project['summary'],
                    'image' => $project['image'],
                    'provider' => SeoHelper::organizationSchema(),
                ],
                SeoHelper::breadcrumbSchema([
                    'Home' => $baseUrl . '/',
                    'Projects' => $baseUrl . '/projects',
                    $project['title'] => $baseUrl . '/projects/' . $slug,
                ]),
            ],
        ];
    }
}
