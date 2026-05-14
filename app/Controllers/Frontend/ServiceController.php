<?php

namespace App\Controllers\Frontend;

use App\Controllers\BaseController;
use App\Repositories\PageRepository;
use App\Repositories\PageSectionRepository;
use App\Repositories\ServiceRepository;
use App\Repositories\SiteSettingRepository;
use App\Services\ServicePageContentService;
use App\Support\DatabaseFactory;

/**
 * Handles public service pages.
 */
class ServiceController extends BaseController
{
    public function __construct(private ?ServicePageContentService $serviceContent = null)
    {
    }

    public function index(): string
    {
        $content = $this->content()->indexContent();

        return $this->view('frontend/pages/services/index', [
            'title' => $content['title'],
            'active' => 'services',
        ] + $content);
    }

    public function show(string $slug): string
    {
        $content = $this->content()->showContent($slug);

        if ($content === null) {
            http_response_code(404);

            return $this->view('frontend/pages/show', [
                'title' => 'Service Not Found',
                'content' => '<p>The requested service page could not be found.</p>',
                'active' => 'services',
            ]);
        }

        return $this->view('frontend/pages/services/show', [
            'title' => $content['title'],
            'active' => 'services',
        ] + $content);
    }

    private function content(): ServicePageContentService
    {
        if ($this->serviceContent !== null) {
            return $this->serviceContent;
        }

        $connection = DatabaseFactory::make();

        return $this->serviceContent = new ServicePageContentService(
            new PageRepository($connection),
            new PageSectionRepository($connection),
            new ServiceRepository($connection),
            new SiteSettingRepository($connection)
        );
    }
}
