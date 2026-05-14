<?php

namespace App\Controllers\Frontend;

use App\Controllers\BaseController;
use App\Helpers\SeoHelper;
use App\Repositories\ProjectRepository;
use App\Services\ProjectService;
use App\Support\DatabaseFactory;
use Throwable;

/**
 * PageController - Handles static pages
 */
class PageController extends BaseController
{
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
        return $this->view('frontend/pages/projects/index', [
            'title' => 'Projects and Gallery',
            'active' => 'projects',
            'projects' => $this->loadProjects(),
            'seo' => [
                'title' => 'Projects and Gallery | Desnky Global Resources',
                'description' => 'View representative project and gallery highlights from Desnky Global Resources across engineering, HSE, procurement and agro sectors.',
                'keywords' => 'Desnky projects, engineering gallery Nigeria, procurement projects, HSE projects Nigeria',
                'canonical' => 'https://www.desnkygroup.com/projects',
                'schema' => [
                    SeoHelper::organizationSchema(),
                    SeoHelper::breadcrumbSchema([
                        'Home' => 'https://www.desnkygroup.com/',
                        'Projects' => 'https://www.desnkygroup.com/projects',
                    ]),
                ],
            ],
        ]);
    }

    public function project(string $slug): string
    {
        $project = $this->loadProject($slug);

        if ($project === null) {
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

        $title = (string) ($project['title'] ?? 'Project');
        $summary = (string) ($project['summary'] ?? $project['description'] ?? '');
        $image = (string) ($project['image'] ?? $project['featured_image'] ?? '');

        return $this->view('frontend/pages/show', [
            'title' => $title,
            'active' => 'projects',
            'content' => '<p>' . e($summary) . '</p>',
            'seo' => [
                'title' => ($project['meta_title'] ?? null) ?: $title . ' | Desnky Projects',
                'description' => ($project['meta_description'] ?? null) ?: $summary,
                'keywords' => ($project['meta_keywords'] ?? null) ?: $title . ', Desnky project, Nigeria',
                'canonical' => 'https://www.desnkygroup.com/projects/' . $slug,
                'image' => $image,
                'schema' => [
                    [
                        '@context' => 'https://schema.org',
                        '@type' => 'CreativeWork',
                        'name' => $title,
                        'description' => $summary,
                        'image' => $image,
                        'provider' => SeoHelper::organizationSchema(),
                    ],
                    SeoHelper::breadcrumbSchema([
                        'Home' => 'https://www.desnkygroup.com/',
                        'Projects' => 'https://www.desnkygroup.com/projects',
                        $title => 'https://www.desnkygroup.com/projects/' . $slug,
                    ]),
                ],
            ],
        ]);
    }

    /**
     * @return array<int, array>
     */
    private function loadProjects(): array
    {
        try {
            $projects = (new ProjectService(new ProjectRepository(DatabaseFactory::make())))->published();

            if ($projects !== []) {
                return $projects;
            }
        } catch (Throwable) {
        }

        return [
            [
                'title' => 'Industrial Engineering Support',
                'category' => 'Engineering',
                'summary' => 'Technical support and coordination for industrial equipment readiness.',
                'image' => 'https://images.unsplash.com/photo-1581092160607-ee22621dd758?auto=format&fit=crop&w=900&q=80',
            ],
            [
                'title' => 'Safety Materials Supply',
                'category' => 'HSE',
                'summary' => 'Supply support for protective equipment and worksite safety materials.',
                'image' => 'https://images.unsplash.com/photo-1504917595217-d4dc5ebe6122?auto=format&fit=crop&w=900&q=80',
            ],
            [
                'title' => 'Procurement Logistics',
                'category' => 'Procurement',
                'summary' => 'Vendor coordination and delivery follow-up for business-critical materials.',
                'image' => 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?auto=format&fit=crop&w=900&q=80',
            ],
            [
                'title' => 'Agro Supply Coordination',
                'category' => 'Agro',
                'summary' => 'Agro product sourcing and supply coordination for local value chains.',
                'image' => 'https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&w=900&q=80',
            ],
        ];
    }

    private function loadProject(string $slug): ?array
    {
        try {
            $project = (new ProjectService(new ProjectRepository(DatabaseFactory::make())))->getPublishedBySlug($slug);

            if ($project !== null) {
                $project['image'] = $project['featured_image'] ?? null;
                return $project;
            }
        } catch (Throwable) {
        }

        foreach ($this->loadProjects() as $project) {
            $candidate = strtolower(trim((string) preg_replace('/[^A-Za-z0-9-]+/', '-', $project['title'] ?? ''), '-'));

            if ($candidate === $slug) {
                $project['slug'] = $slug;
                return $project;
            }
        }

        return null;
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
