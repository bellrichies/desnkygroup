<?php

namespace App\Controllers\Frontend;

use App\Controllers\BaseController;
use App\Repositories\PageRepository;
use App\Repositories\PageSectionRepository;
use App\Repositories\ProjectRepository;
use App\Repositories\HeroSliderRepository;
use App\Repositories\ServiceRepository;
use App\Repositories\SiteSettingRepository;
use App\Repositories\TrustedClientRepository;
use App\Services\HomeContentService;
use App\Support\DatabaseFactory;

/**
 * HomeController - Handles public home page
 */
class HomeController extends BaseController
{
    public function __construct(private ?HomeContentService $homeContent = null)
    {
    }

    /**
     * Display home page
     *
     * @return string
     */
    public function index(): string
    {
        $content = $this->content()->getContent();

        return $this->view('frontend/pages/home', [
            'title' => $content['title'],
            'active' => 'home',
            'csrf_token' => $this->csrf(),
        ] + $content);
    }

    private function content(): HomeContentService
    {
        if ($this->homeContent !== null) {
            return $this->homeContent;
        }

        $connection = DatabaseFactory::make();

        return $this->homeContent = new HomeContentService(
            new PageRepository($connection),
            new PageSectionRepository($connection),
            new ServiceRepository($connection),
            new ProjectRepository($connection),
            new SiteSettingRepository($connection),
            new HeroSliderRepository($connection),
            new TrustedClientRepository($connection)
        );
    }
}
