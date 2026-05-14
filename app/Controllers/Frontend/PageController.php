<?php

namespace App\Controllers\Frontend;

use App\Controllers\BaseController;
use App\Helpers\SeoHelper;
use App\Repositories\PageRepository;
use App\Repositories\PageSectionRepository;
use App\Repositories\ProjectRepository;
use App\Repositories\SiteSettingRepository;
use App\Services\ProjectPageContentService;
use App\Support\DatabaseFactory;

/**
 * PageController - Handles static pages
 */
class PageController extends BaseController
{
    public function __construct(private ?ProjectPageContentService $projectContent = null)
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
        return $this->view('frontend/pages/show', [
            'title' => 'About Desnky Global Resources Ltd',
            'active' => 'about',
            'content' => '<p>Desnky Global Resources Ltd is a Nigerian corporate services company supporting organizations across engineering, energy, procurement, HSE, ICT and agro value chains.</p><p>Our work is built around practical delivery, clear communication, safety awareness and dependable sourcing for business-critical needs.</p>',
            'seo' => [
                'title' => 'About Desnky Global Resources Ltd',
                'description' => 'Learn about Desnky Global Resources Ltd, a Nigerian company providing engineering, energy, procurement, HSE, ICT and agro services.',
                'keywords' => 'Desnky Global Resources, Nigerian engineering company, procurement company Lagos, energy services Nigeria',
                'canonical' => 'https://www.desnkygroup.com/about',
                'schema' => [
                    SeoHelper::organizationSchema(),
                    SeoHelper::breadcrumbSchema([
                        'Home' => 'https://www.desnkygroup.com/',
                        'About' => 'https://www.desnkygroup.com/about',
                    ]),
                ],
            ],
        ]);
    }

    public function hse(): string
    {
        return $this->view('frontend/pages/show', [
            'title' => 'HSE Policy',
            'active' => 'services',
            'content' => '<p>Health, safety and environmental responsibility are central to how Desnky Global Resources Ltd plans and delivers work. We support safe operations through risk awareness, appropriate protective equipment, responsible supervision and continuous improvement.</p><p>Our HSE approach prioritizes people, assets, communities and the environment throughout each engagement.</p>',
            'seo' => [
                'title' => 'HSE Policy | Desnky Global Resources Ltd',
                'description' => 'Read the Desnky Global Resources Ltd health, safety and environment commitment for Nigerian business operations.',
                'keywords' => 'HSE policy Nigeria, safety services Nigeria, health safety environment',
                'canonical' => 'https://www.desnkygroup.com/hse-policy',
                'schema' => [
                    SeoHelper::organizationSchema(),
                    SeoHelper::breadcrumbSchema([
                        'Home' => 'https://www.desnkygroup.com/',
                        'HSE Policy' => 'https://www.desnkygroup.com/hse-policy',
                    ]),
                ],
            ],
        ]);
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

    /**
     * Display a page by slug
     *
     * @param string $slug Page slug
     * @return string
     */
    public function show(string $slug): string
    {
        // In Phase 2+, would query database for page
        // For now, return demo content

        return $this->view('frontend/pages/show', [
            'title' => ucfirst(str_replace('-', ' ', $slug)),
            'slug' => $slug,
            'content' => "Content for page: {$slug}",
        ]);
    }
}
