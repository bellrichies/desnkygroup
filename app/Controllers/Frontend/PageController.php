<?php

namespace App\Controllers\Frontend;

use App\Controllers\BaseController;
use App\Helpers\SeoHelper;
use App\Repositories\PageRepository;
use App\Repositories\PageSectionRepository;
use App\Repositories\ProjectRepository;
use App\Repositories\SiteSettingRepository;
use App\Services\AboutContentService;
use App\Services\HseContentService;
use App\Services\ProjectPageContentService;
use App\Support\DatabaseFactory;

/**
 * PageController - Handles static pages
 */
class PageController extends BaseController
{
    public function __construct(
        private ?ProjectPageContentService $projectContent = null,
        private ?AboutContentService $aboutContent = null,
        private ?HseContentService $hseContent = null
    )
    {
    }

    public function redirectToServices(): void
    {
        $this->redirect('/services', 301);
    }

    public function redirectToContact(): void
    {
        $this->redirect('/contact', 301);
    }

    public function redirectToHse(): void
    {
        $this->redirect('/hse-policy', 301);
    }

    public function redirectToProjects(): void
    {
        $this->redirect('/projects', 301);
    }

    public function about(): string
    {
        $content = $this->aboutContent()->getContent();

        return $this->view('frontend/pages/about', [
            'title' => $content['title'],
            'active' => 'about',
        ] + $content);
    }

    public function hse(): string
    {
        $content = $this->hseContent()->getContent();

        return $this->view('frontend/pages/hse', [
            'title' => $content['title'],
            'active' => 'services',
        ] + $content);
    }

    public function projects(): string
    {
        $content = $this->projectContent()->indexContent();

        return $this->view('frontend/pages/projects/index', [
            'title' => $content['title'],
            'active' => 'projects',
        ] + $content);
    }

    public function project(string $slug): string
    {
        $content = $this->projectContent()->showContent($slug);

        if ($content === null) {
            http_response_code(404);

            return $this->view('frontend/pages/show', [
                'title' => 'Project Not Found',
                'content' => '<p>The requested project page could not be found.</p>',
                'active' => 'projects',
                'seo' => [
                    'title' => 'Project Not Found | Desnky Global Resources',
                    'description' => 'The requested Desnky Global Resources project could not be found.',
                    'robots' => 'noindex, follow',
                ],
            ]);
        }

        return $this->view('frontend/pages/projects/show', [
            'title' => $content['title'],
            'active' => 'projects',
        ] + $content);
    }

    private function projectContent(): ProjectPageContentService
    {
        if ($this->projectContent !== null) {
            return $this->projectContent;
        }

        $connection = DatabaseFactory::make();

        return $this->projectContent = new ProjectPageContentService(
            new PageRepository($connection),
            new PageSectionRepository($connection),
            new ProjectRepository($connection),
            new SiteSettingRepository($connection)
        );
    }

    private function aboutContent(): AboutContentService
    {
        if ($this->aboutContent !== null) {
            return $this->aboutContent;
        }

        $connection = DatabaseFactory::make();

        return $this->aboutContent = new AboutContentService(
            new PageRepository($connection),
            new PageSectionRepository($connection),
            new SiteSettingRepository($connection)
        );
    }

    private function hseContent(): HseContentService
    {
        if ($this->hseContent !== null) {
            return $this->hseContent;
        }

        $connection = DatabaseFactory::make();

        return $this->hseContent = new HseContentService(
            new PageRepository($connection),
            new PageSectionRepository($connection),
            new SiteSettingRepository($connection)
        );
    }

    /**
     * Display a page by slug
     *
     * @param string $slug Page slug
     * @return string
     */
    public function show(string $slug): string
    {
        $connection = DatabaseFactory::make();
        $page = (new PageRepository($connection))->findPublishedBySlug($slug);

        if ($page === null) {
            http_response_code(404);

            return $this->view('frontend/pages/show', [
                'title' => 'Page Not Found',
                'active' => '',
                'content' => '<p>The requested page could not be found.</p>',
                'seo' => [
                    'title' => 'Page Not Found | Desnky Global Resources',
                    'description' => 'The requested Desnky Global Resources page could not be found.',
                    'robots' => 'noindex, follow',
                ],
            ]);
        }

        $settings = (new SiteSettingRepository($connection))->publicSettings();
        $baseUrl = rtrim((string) ($settings['site.url'] ?? 'https://www.desnkygroup.com'), '/');

        return $this->view('frontend/pages/show', [
            'title' => (string) ($page['meta_title'] ?: $page['title']),
            'active' => (string) $page['slug'],
            'content' => (string) ($page['content'] ?? ''),
            'seo' => [
                'title' => (string) ($page['meta_title'] ?: $page['title']),
                'description' => (string) ($page['meta_description'] ?: $page['excerpt'] ?: ''),
                'keywords' => (string) ($page['meta_keywords'] ?: ''),
                'canonical' => $baseUrl . '/' . $slug,
                'image' => (string) ($page['featured_image'] ?? ''),
                'schema' => [
                    SeoHelper::organizationSchema(),
                    SeoHelper::breadcrumbSchema([
                        'Home' => $baseUrl . '/',
                        (string) $page['title'] => $baseUrl . '/' . $slug,
                    ]),
                ],
            ],
        ]);
    }
}
