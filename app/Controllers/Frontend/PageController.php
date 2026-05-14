<?php

namespace App\Controllers\Frontend;

use App\Controllers\BaseController;
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
                'canonical' => 'https://www.desnkygroup.com/about',
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
                'canonical' => 'https://www.desnkygroup.com/hse-policy',
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
                'canonical' => 'https://www.desnkygroup.com/projects',
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
